<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\PaymentProcessorAdaptersFactory;
use App\Service\PriceCalculator;
use App\DTO\CalculatePriceRequest;
use App\DTO\PurchaseRequest;


final class ApiPaymentController extends AbstractController
{
    public function __construct(
        private PriceCalculator $priceCalculator,
        private ValidatorInterface $validator
    )
    {}



    //#[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculatePrice(
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse
    {
        try {
            $dto = $serializer->deserialize(
                $request->getContent(),
                CalculatePriceRequest::class,
                'json'
            );
            
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                return $this->json($errors, 400);
            }

            $price = $this->priceCalculator->calculate($dto);
            return $this->json(['price' => $price]);
            
        } catch (\Exception $error) {
            return $this->json(['error' => $error->getMessage()], 400);
        }
    }



    //#[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(
        Request $request,
        PaymentProcessorAdaptersFactory $paymentProcessorAdaptersFactory
    ): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);

            // Request parsing is without Serializer, because PurchaseRequest is nested DTO.
            $dtoCalculatePriceRequest = new CalculatePriceRequest();
            $dtoCalculatePriceRequest->product    = $requestData['product'] ?? null;
            $dtoCalculatePriceRequest->taxNumber  = $requestData['taxNumber'] ?? null;
            $dtoCalculatePriceRequest->couponCode = $requestData['couponCode'] ?? null;

            // Build PurchaseRequest with CalculatePriceRequest injection.
            $dtoPurchaseRequest = new PurchaseRequest();
            $dtoPurchaseRequest->calculatePriceRequest = $dtoCalculatePriceRequest;
            $dtoPurchaseRequest->paymentProcessor      = $requestData['paymentProcessor'] ?? null;
            
            $errors = $this->validator->validate($dtoPurchaseRequest);
            if ($errors->count() > 0) {
                return $this->json($errors, 400);
            }

            $price = $this->priceCalculator->calculate($dtoCalculatePriceRequest);
            $paymentProcessor = $paymentProcessorAdaptersFactory->create($dtoPurchaseRequest->paymentProcessor);
            $response = $paymentProcessor->processPayment($price);

            return $this->json([
                'success' => $response->getStatus(),
                'message' => $response->getMessage()
            ]);
            
        } catch (\Exception $error) {
            return $this->json(['error' => $error->getMessage()], 400);
        }
    }
}
