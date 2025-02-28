<?php
namespace App\Service\PaymentProcessorAdapter;

use App\Service\PaymentProcessorAdapter\PaymentProcessorResponse;

interface PaymentProcessorAdapterInterface
{
    public function processPayment(int $amountCents): PaymentProcessorResponse;
}