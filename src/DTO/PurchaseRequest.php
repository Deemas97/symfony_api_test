<?php
namespace App\DTO;

use App\DTO\CalculatePriceRequest;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequest
{
    #[Assert\NotBlank]
    #[CalculatePriceRequest]
    public ?CalculatePriceRequest $calculatePriceRequest = null;

    #[Assert\NotBlank]
    public ?string $paymentProcessor = null;
}