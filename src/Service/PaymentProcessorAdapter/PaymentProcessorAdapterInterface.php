<?php
namespace App\Service\PaymentProcessorAdapter;

use App\Service\PaymentProcessorAdapter\PaymentProcessorResponse;

interface PaymentProcessorAdapterInterface
{
    public function processPayment(float $amount): PaymentProcessorResponse;
}