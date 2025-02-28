<?php
namespace App\Service\PaymentProcessorAdapter;

use App\Service\PaymentProcessorAdapter\PaymentProcessorAdapterInterface;
use App\Service\PaymentProcessorAdapter\PaymentProcessorResponse;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

use App\Exception\PaymentProcessingException;

class StripePaymentProcessorAdapter implements PaymentProcessorAdapterInterface
{
    public function __construct(
        private StripePaymentProcessor $processorOrigin
    )
    {}

    public function processPayment(int $amountCents): PaymentProcessorResponse
    {
        $amount = $amountCents / 100;
        
        if ($this->processorOrigin->processPayment($amount) === true) {
            $response = new PaymentProcessorResponse();
            $response->setSuccessStatus();
            $response->setMessage('Stripe payment processed successfully');

            return $response;
        } else {
            throw new PaymentProcessingException('Stripe payment failed, need amount from 100 or more');
        }
    }
}