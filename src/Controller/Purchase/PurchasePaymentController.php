<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController 
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        
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
            return $this->redirectToRoute('cart_show');
        }
        
        $intent = $stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' =>$purchase,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }
    
}