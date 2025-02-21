<?php
namespace App\Service\PaymentProcessorAdapter;

use App\Service\PaymentProcessorAdapter\PaymentProcessorAdapterInterface;
use App\Service\PaymentProcessorAdapter\PaymentProcessorResponse;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripePaymentProcessorAdapter implements PaymentProcessorAdapterInterface
{
    public function processPayment(float $amount): PaymentProcessorResponse
    {
        $processorOrigin = new StripePaymentProcessor();
        $response = new PaymentProcessorResponse();
        
        if ($processorOrigin->processPayment($amount) === true) {
            $response->setSuccessStatus();
            $response->setMessage('Stripe payment processed successfully');
        } else {
            $response->setMessage('Stripe payment failed, need amount from 100 or more');
        }

        return $response;
    }
}