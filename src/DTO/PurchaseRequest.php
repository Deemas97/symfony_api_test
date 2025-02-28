<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\TaxNumber;

class PurchaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $product;

    #[Assert\NotBlank]
    #[TaxNumber]
    public ?string $taxNumber;

    #[Assert\Regex('/^[A-Z]\d+$/')]
    public ?string $couponCode = null;

    #[Assert\NotBlank]
    public ?string $paymentProcessor = null;
}