<?php
namespace App\Service\PaymentProcessorAdapter;

use App\Service\PaymentProcessorAdapter\PaymentProcessorAdapterInterface;
use App\Service\PaymentProcessorAdapter\PaymentProcessorResponse;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

use Exception;
use App\Exception\PaymentProcessingException;

class PaypalPaymentProcessorAdapter implements PaymentProcessorAdapterInterface
{
    public function __construct(
        private PaypalPaymentProcessor $processorOrigin
    )
    {}

    public function processPayment(int $amountCents): PaymentProcessorResponse
    {
        try {
            $this->processorOrigin->pay($amountCents);

            $response = new PaymentProcessorResponse();
            $response->setSuccessStatus();
            $response->setMessage('Paypal payment processed successfully');
            
            return $response;
        } catch (Exception $error) {
            throw new PaymentProcessingException("Paypal payment processing error: " . $error->getMessage());
        }
    }
}