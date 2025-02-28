<?php
namespace App\Service;

use Psr\Container\ContainerInterface;
use App\Service\PaymentProcessorAdapter\PaymentProcessorAdapterInterface;
use App\Service\PaymentProcessorAdapter\PaypalPaymentProcessorAdapter;
use App\Service\PaymentProcessorAdapter\StripePaymentProcessorAdapter;
use InvalidArgumentException;

class PaymentProcessorAdaptersFactory
{
    // Use Locator for direct injection
    public function __construct(private ContainerInterface $adapterLocator)
    {}

    public function create(?string $type): PaymentProcessorAdapterInterface
    {
        return match (strtolower($type)) {
            'paypal' => $this->adapterLocator->get(PaypalPaymentProcessorAdapter::class),
            'stripe' => $this->adapterLocator->get(StripePaymentProcessorAdapter::class),
            default  => throw new InvalidArgumentException("Unknown payment processor: $type"),
        };
    }
}