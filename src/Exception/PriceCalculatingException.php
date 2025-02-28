<?php
namespace App\Exception;

use Exception;

class PriceCalculatingException extends Exception
{
    public function __construct($message = "Price calculating error", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}