<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;
    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        
        $faker =Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        // generate admin
        $admin = new User;
        // hachage du password
        $hash = $this->encoder->encodePassword($admin, "password");
        // set admin
        $admin->setEmail("admin@gmail.com")
        ->setFullName('Admin')
        ->setPassword($hash)
        ->setRoles([
            'ROLE_ADMIN'
        ]);
        $manager->persist($admin);
        
        // generate users
        $users = [];
        
        for($u = 0 ; $u < 5 ; $u++){
            $user = new User();
            // hachage du password
            $hash = $this->encoder->encodePassword($user, "password");
            $user->setEmail("user$u@gmail.com")
            ->setFullName($faker->name())
            ->setPassword($hash);
            
            $users[] = $user;
            
            $manager->persist($user);
        }

        $products = [];

        // generate category
        for($c = 0; $c < 3;$c++)
        {
            $category = new Category;
            // department donné par la librairie Bezhanov
            $category->setName($faker->department)
            ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for($p = 0 ; $p < mt_rand(15, 20) ; $p++)
            {
                $product = new Product;
                $product->setName($faker->productName);
                $product->setPrice($faker->price(4000, 20000));
                $product->setSlug(strtolower($this->slugger->slug($product->getName())))
                ->setCategory($category)
                ->setShortDescription($faker->paragraph())
                ->setPicture($faker->imageUrl(400, 400, true));

                $products[] = $product;

                $manager->persist($product);
            }
        }
        
        // generate purchase
        for($p = 0; $p < mt_rand(20, 40); $p++){
            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
            ->setAddress($faker->streetAddress)
            ->setPostalCode($faker->postcode)
            ->setCity($faker->city)
            ->setUser($faker->randomElement($users))
            ->setTotal(mt_rand(2000, 30000))
            ->setPurchasedAt($faker->dateTimeBetween('-6 months', 'now'));

            // generate random products 
            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));

            foreach($selectedProducts as $product){

                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($product)
                ->setQuantity(mt_rand(1,3))
                ->setProductName($product->getName())
                ->setProductPrice($product->getPrice())
                ->setTotal(
                    $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                    )
                ->setPurchase($purchase);
                
                $manager->persist($purchaseItem);
                
            }
            
            if($faker->boolean(90)){
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $manager->persist($purchase);
        }
        
        $manager->flush();
    }
}