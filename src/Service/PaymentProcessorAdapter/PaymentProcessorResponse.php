<?php
namespace App\Service\PaymentProcessorAdapter;

class PaymentProcessorResponse
{
    private bool $status = false;
    private string|array $message;

    public function getStatus()
    {
        return $this->status;
    }

    public function setSuccessStatus()
    {
        $this->status = true;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}