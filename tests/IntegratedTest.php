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
        $response = json_decode($client->getResponse()->getContent(), true);
        ////

        dump($response);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('price', $response);
        $this->assertEquals(10710, $response['price']);
    }


    
    public function testApiMethodPurchasePaypal(): void
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
        $response = json_decode($client->getResponse()->getContent(), true);
        ////

        dump($response);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
    }



    public function testApiMethodPurchaseStripe(): void
    {
        $client = static::createClient();

        $jsonData = [
            'product' => 3,
            'taxNumber' => 'FRXY123456789',
            'couponCode' => 'C30',
            'paymentProcessor' => 'stripe'
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
        $response = json_decode($client->getResponse()->getContent(), true);
        ////

        dump($response);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
    }
}