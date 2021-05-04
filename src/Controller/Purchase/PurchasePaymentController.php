<?php 

namespace App\Controller\Purchase;

use App\Repository\PurchaseItemRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController 
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     */
    public function showCardForm($id, PurchaseItemRepository $purchaseItemRepository)
    {
        
        $purchase = $purchaseItemRepository->find($id);

        if(!$purchase){
            return $this->redirectToRoute('cart_show');
        }
        
        \Stripe\Stripe::setApiKey('sk_test_51In1kpGraWjWiqTvxZkcYJYBeG4vXkczITNg39HIxYYoKeEpcJq77N6qZwevKFsGveaHAeo1TEcnZqjHhC1M4HFh00gcbbmj1Z');

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'
        ]);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret
        ]);
    }
    
}