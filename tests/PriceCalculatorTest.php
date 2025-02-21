<?php
namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Entity\Product;
use App\Entity\Coupon;
use App\Service\PriceCalculator;
use App\DTO\CalculatePriceRequest;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;

class PriceCalculatorTest extends TestCase
{
    private function createMockProductRepository(Product $product): ProductRepository
    {
        $mock = $this->createMock(ProductRepository::class);
        $mock->method('find')
            ->willReturn($product);
        return $mock;
    }

    private function createMockCouponRepository(?Coupon $coupon = null): CouponRepository
    {
        $mock = $this->createMock(CouponRepository::class);
        $mock->method('find')
            ->willReturn($coupon);
        return $mock;
    }

    public function testPriceCalculationWithoutCoupon()
    {
        $product = new Product();
        $product->setName('Item');
        $product->setPrice(100); // 100.00 EUR
        
        $calculator = new PriceCalculator(
            $this->createMockProductRepository($product),
            $this->createMockCouponRepository()
        );

        $request = new CalculatePriceRequest();
        $request->product = 1;
        $request->taxNumber = 'DE123456789'; // Германия (19% налог)

        // ACTION
        $price = $calculator->calculate($request);
        ////

        // (100 EUR + 19%) = 119 EUR
        $this->assertEquals(119.0, $price);
    }

    public function testPriceCalculationWithPercentCoupon()
    {
        // Arrange
        $product = new Product();
        $product->setPrice(100);
        
        $coupon = new Coupon();
        $coupon->setCode('P10');
        $coupon->setType('percent');
        $coupon->setValue(10.0);

        $calculator = new PriceCalculator(
            $this->createMockProductRepository($product),
            $this->createMockCouponRepository($coupon)
        );

        $request = new CalculatePriceRequest();
        $request->product = 1;
        $request->taxNumber = 'DE123456789';
        $request->couponCode = 'P10';

        // ACTION
        $price = $calculator->calculate($request);
        ////

        // ((100 EUR - 10%) + 19%) = 107.1 EUR
        $this->assertEquals(107.1, $price);
    }

    public function testPriceCalculationWithFixedCoupon()
    {
        $product = new Product();
        $product->setPrice(100);
        
        $coupon = new Coupon();
        $coupon->setCode('F20');
        $coupon->setType('fixed');
        $coupon->setValue(20.0);

        $calculator = new PriceCalculator(
            $this->createMockProductRepository($product),
            $this->createMockCouponRepository($coupon)
        );

        $request = new CalculatePriceRequest();
        $request->product = 1;
        $request->taxNumber = 'IT12345678900'; // Италия (22% налог)
        $request->couponCode = 'F20';

        // ACTION
        $price = $calculator->calculate($request);
        ////

        // ((100 EUR - 20 EUR) + 22%) = 97.6 EUR
        $this->assertEquals(97.6, $price);
    }
}