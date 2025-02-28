<?php
namespace App\Exception;

use Exception;

class PaymentProcessingException extends Exception
{
    public function __construct($message = "Payment processing error", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}