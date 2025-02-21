<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IntegratedTest extends WebTestCase
{
    public function testApiMethodCalculatePrice(): void
    {
        $client = static::createClient();

        $jsonData = [
            'product' => 1,
            'taxNumber' => 'DE123456789',
            'couponCode' => 'C10'
        ];

        $client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($jsonData)
        );

        // ACTION
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        ////

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('price', $responseData);
        $this->assertTrue(is_float($responseData['price']));
        $this->assertEquals(107.1, $responseData['price']);
    }


    
    public function testApiMethodPurchase(): void
    {
        $client = static::createClient();

        $jsonData = [
            'product' => 2,
            'taxNumber' => 'IT12345678900',
            'couponCode' => 'C20',
            'paymentProcessor' => 'paypal'
        ];

        $client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($jsonData)
        );

        // ACTION
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        ////

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);
    }
}