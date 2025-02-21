<?php
namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Entity\Product;
use App\Entity\Coupon;
use App\Service\PaymentProcessorAdaptersFactory;
use App\Service\PriceCalculator;
use App\DTO\CalculatePriceRequest;
use App\DTO\PurchaseRequest;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;

class PurchaseTest extends TestCase
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

    public function testPurchasePaypal()
    {
        $product = new Product();
        $product->setName('Item');
        $product->setPrice(100);
        
        $calculator = new PriceCalculator(
            $this->createMockProductRepository($product),
            $this->createMockCouponRepository()
        );

        $requestPriceCalculator = new CalculatePriceRequest();
        $requestPriceCalculator->product = 1;
        $requestPriceCalculator->taxNumber = 'DE123456789'; // Германия (19% налог)

        $price = $calculator->calculate($requestPriceCalculator);

        $request = new PurchaseRequest();
        $request->calculatePriceRequest = $requestPriceCalculator;
        $request->paymentProcessor = 'paypal';

        // ACTION
        $paymentProcessorAdaptorsFactory = new PaymentProcessorAdaptersFactory();
        $paymentProcessorAdapter = $paymentProcessorAdaptorsFactory->create($request->paymentProcessor);
        ////

        $response = $paymentProcessorAdapter->processPayment($price);

        $this->assertEquals(true, $response->getStatus());
    }
}