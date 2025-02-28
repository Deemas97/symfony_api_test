<?php

namespace App\DataFixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\Coupon;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; ++$i) {
            $product = new Product();
            $product->setName($faker->words(2, true));
            $product->setPrice($i * 10000);
            
            $manager->persist($product);
        }

        $manager->flush();

        for ($i = 1; $i <= 10; ++$i) {
            $coupon = new Coupon();
            $coupon->setCode('C' . ($i * 10));
            $coupon->setType((rand(0, 1) % 2) ? 'fixed' : 'percent');
            $coupon->setValue($i * 10);
            
            $manager->persist($coupon);
        }

        $manager->flush();
    }
}
