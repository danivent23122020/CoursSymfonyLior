<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($p = 0 ; $p < 100 ; $p++)
        {
            $product = new Product;
            $product->setName("Product");
            $product->setPrice(mt_rand(100, 200));
            $product->setSlug("produit-n-$p");

            $manager->persist($product);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}