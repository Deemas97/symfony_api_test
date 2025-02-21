<?php
namespace App\Service;

use App\Service\PaymentProcessorAdapter\PaymentProcessorAdapterInterface;
use App\Service\PaymentProcessorAdapter\PaypalPaymentProcessorAdapter;
use App\Service\PaymentProcessorAdapter\StripePaymentProcessorAdapter;

class PaymentProcessorAdaptersFactory
{
    public function create(?string $type): PaymentProcessorAdapterInterface
    {
        return match (strtolower($type)) {
            'paypal' => new PaypalPaymentProcessorAdapter(),
            'stripe' => new StripePaymentProcessorAdapter(),
            default => throw new \InvalidArgumentException("Unknown payment processor:{$type}"),
        };
    }
}