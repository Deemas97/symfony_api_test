<?php
namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\DTO\CalculatePriceRequest;
use App\DTO\PurchaseRequest;
Use App\Entity\Coupon;

use InvalidArgumentException;
use App\Exception\PriceCalculatingException;

class PriceCalculator
{
    public function __construct(
        private ProductRepository $productRepo,
        private CouponRepository $couponRepo
    )
    {}

    public function calculate(CalculatePriceRequest|PurchaseRequest $request): float
    {
        $productId = $request->product ?? 'NULL';
        $product = $this->productRepo->find($productId);
        if (!$product) {
            throw new InvalidArgumentException("Product id: $productId is not found");
        }

        $priceCents = $product->getPrice();
        
        // Find coupon (required)
        $couponCode = $request->couponCode ?? 'NULL';
        $coupon = $this->couponRepo->findBy(['code' => $couponCode])[0];
        if (!$coupon) {
            throw new InvalidArgumentException("Invalid coupon id: $couponCode");
        }
        
        // Calculate price with coupon
        $priceCents = $this->applyCoupon($priceCents, $coupon);

        // Find tax rate and calculate price with it
        $taxRate = $this->getTaxRate($request->taxNumber);
        $priceCents = $priceCents * (1 + $taxRate);

        return $priceCents;
    }

    private function applyCoupon(int $priceCents, Coupon $coupon): int
    {
        $couponType = strtolower($coupon->getType());
        return match ($couponType) {
            'fixed'   => $this->calcWithFixedCoupon($priceCents, $coupon->getValue()),
            'percent' => $this->calcWithPercentedCoupon($priceCents, $coupon->getValue()),
            default   => throw new InvalidArgumentException("Invalid coupon type: $couponType"),
        };
    }

    private function calcWithFixedCoupon(int $priceCents, float $fixedValue): int
    {
        $priceCentsTotal = ($priceCents - ($fixedValue * 100));

        if ($priceCentsTotal < 0) {
            throw new PriceCalculatingException("Price is negative after applying coupon with fixed value");
        }

        return $priceCentsTotal;
    }

    private function calcWithPercentedCoupon(int $priceCents, float $percents): int
    {
        $priceCentsTotal = ($priceCents * (1 - $percents / 100));

        if ($priceCentsTotal < 0) {
            throw new PriceCalculatingException("Price is negative after applying coupon with percented value");
        }

        return $priceCentsTotal;
    }

    private function getTaxRate(string $taxNumber): float
    {
        $countryCode = strtoupper(substr($taxNumber, 0, 2));
        
        return match ($countryCode) {
            'DE'    => 0.19,
            'IT'    => 0.22,
            'FR'    => 0.20,
            'GR'    => 0.24,
            default => throw new InvalidArgumentException("Unknown country code: $countryCode"),
        };
    }
}