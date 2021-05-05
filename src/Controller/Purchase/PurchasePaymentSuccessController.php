<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService)
    {
        // 1 je récupère la commande
        $purchase = $purchaseRepository->find($id);

        // conditions de non validation de la commande avec if
        if(
            // si il n'y a pas de purchase
            !$purchase || 
            // OU si je ne suis pas l'utilisateur à qui appartient cette purchase
            ($purchase && $purchase->getUser() !== $this->getUser()) || 
            // OU si la purchase est déjà payée
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID))
            {
            $this->addFlash('warning', "La commande n'existe pas");
            return $this->redirectToRoute("purchase_index");
            }
        
        // 2 je la fais passer au status PAYEE (PAID)
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();
        
        // 3 je vide le panier
        $cartService->empty();
        
        // 4 je redirige avec un flash vers la liste des commandes
        $this->addFlash('success', "La commande a été payée et confirmée !");
        return $this->redirectToRoute("purchase_index");
    }
}