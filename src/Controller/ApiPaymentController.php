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

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;

final class ApiPaymentController extends AbstractController
{
    public function __construct(
        private PriceCalculator $priceCalculator,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    )
    {}



    public function calculatePrice(
        Request $request
    ): JsonResponse
    {
        try {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                CalculatePriceRequest::class,
                'json'
            );
            
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                throw new ValidationException(
                    "Invalid input data",
                    $this->parseValidatorErrors($errors)
                );
            }

            $price = $this->priceCalculator->calculate($dto);
            return $this->json(['price' => $price]);
            
        } catch (Exception $error) {
            return $this->json(['error' => $error->getMessage()], 400);
        }
    }


    
    public function purchase(
        Request $request,
        PaymentProcessorAdaptersFactory $paymentProcessorAdaptersFactory
    ): JsonResponse
    {
        try {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                PurchaseRequest::class,
                'json'
            );
            
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                throw new ValidationException(
                    "Invalid input data",
                    $this->parseValidatorErrors($errors)
                );
            }

            $paymentProcessor = $paymentProcessorAdaptersFactory->create($dto->paymentProcessor);
            $price = $this->priceCalculator->calculate($dto);

            $response = $paymentProcessor->processPayment($price);
            return $this->json([
                'success' => $response->getStatus(),
                'message' => $response->getMessage()
            ]);
            
        } catch (Exception $error) {
            return $this->json(['error' => $error->getMessage()], 400);
        }
    }



    private function parseValidatorErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errorMessages;
    }
}
