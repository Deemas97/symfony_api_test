<?php
namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\DTO\CalculatePriceRequest;
Use App\Entity\Coupon;
use \InvalidArgumentException;

class PriceCalculator
{
    public function __construct(
        private ProductRepository $productRepo,
        private CouponRepository $couponRepo
    )
    {}

    public function calculate(CalculatePriceRequest $request): float
    {
        $product = $this->productRepo->find($request->product);
        if (!$product) {
            throw new InvalidArgumentException("Product id:{$request->product} not found");
        }

        // Convert to cents for integer processing
        $priceCents = round($product->getPrice() * 100, 0);
        
        if ($request->couponCode) {
            $coupon = $this->couponRepo->find($request->couponCode);
            if (!$coupon) {
                throw new InvalidArgumentException("Invalid coupon id:{$request->couponCode}");
            }
            
            $priceCents = $this->applyCoupon($priceCents, $coupon);
        }

        $taxRate = $this->getTaxRate($request->taxNumber);
        $priceCents = $priceCents * (1 + $taxRate);

        // Convert from cents
        return round(($priceCents / 100), 2);
    }

    private function applyCoupon(int $priceCents, Coupon $coupon): int
    {
        $couponType = strtolower($coupon->getType());
        switch ($couponType) {
            case 'fixed':
                return ($priceCents - ($coupon->getValue() * 100));
            case 'percent':
                return ($priceCents * (1 - $coupon->getValue() / 100));
            default:
                throw new InvalidArgumentException("Invalid coupon type:{$couponType}");
        }
    }

    private function getTaxRate(string $taxNumber): float
    {
        $countryCode = substr($taxNumber, 0, 2);
        return match ($countryCode) {
            'DE' => 0.19,
            'IT' => 0.22,
            'FR' => 0.20,
            'GR' => 0.24,
            default => throw new InvalidArgumentException("Unknown country code:{$countryCode}"),
        };
    }
}