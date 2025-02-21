<?php
namespace App\Service\PaymentProcessorAdapter;

use App\Service\PaymentProcessorAdapter\PaymentProcessorAdapterInterface;
use App\Service\PaymentProcessorAdapter\PaymentProcessorResponse;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalPaymentProcessorAdapter implements PaymentProcessorAdapterInterface
{
    public function processPayment(float $amount): PaymentProcessorResponse
    {
        $processorOrigin = new PaypalPaymentProcessor();
        $response = new PaymentProcessorResponse();

        try {
            $processorOrigin->pay($amount);

            $response->setSuccessStatus();
            $response->setMessage('Paypal payment processed successfully');
        } catch (\Exception $error) {
            $response->setMessage($error->getMessage());
        }

        return $response;
    }
}