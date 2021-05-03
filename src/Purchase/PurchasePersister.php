<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{

    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }
    
    public function storePurchase(Purchase $purchase)
    {
        // intégrer tout ce qu'il faut pour passer la purchase et la persister
        // 6 nous allons la lier avec l'utilisateur actuellement connecté -> Security
        $purchase->setUser($this->security->getUser())
        ->setPurchasedAt(new DateTime())
        ->setTotal($this->cartService->getTotal());
        
        $this->em->persist($purchase);
        
        // 7 nous allons la lier avec les produits qui sont dans le panier -> CartService
        foreach($this->cartService->getDetailedCartItems() as $cartItem){
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
            ->setProduct($cartItem->product)
            ->setProductName($cartItem->product->getPrice())
            ->setQuantity($cartItem->qty)
            ->setTotal($cartItem->getTotal())
            ->setProductPrice($cartItem->product->getPrice());

            $this->em->persist($purchaseItem);
        }
        
        // 8 nous allons enregistrer la commande -> EntityManagerInterface
        $this->em->flush();
    }
}