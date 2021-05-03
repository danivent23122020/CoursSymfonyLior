<?php 

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationCotroller extends AbstractController
{
    protected $cartService;
    protected $em;
    protected $persister;
    
    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister)
    {
        $this->cartService = $cartService;
        $this->em = $em;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request)
    {
        // 1 nous voulons lire les données du formulaire -> Request
        $form =$this->createForm(CartConfirmationType::class);
        
        $form->handleRequest($request);
        
        // 2 si le formulaire n'a pas été soumis -> dégager
        if(!$form->isSubmitted()){
            // message Flash puis redirection -> addFlash
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            
            return $this->redirectToRoute('cart_show');
        }
        
        // 3 si je ne suis pas connecté -> dégager
        $user = $this->getUser();
        
        // 4 si il n'y a pas de produits dans mon panier -> dégager -> CartService
        
        $cartItems = $this->cartService->getDetailedCartItems();
        
        if(count($cartItems) === 0){
            
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');

            return $this->redirectToRoute('cart_show');
        }

        // 5 nous allons créer une Purchase
        /** @var Purchase */
        $purchase = $form->getData();
        
        // vient de PurchasePersister.php
        $this->persister->storePurchase($purchase);

        // 9 vider le panier après enregistrement
        $this->cartService->empty();
        
        // message de d'enregistrement
        $this->addFlash('success', 'La commande a bien été enregistrée' );
        
        // redirection 
        return $this->redirectToRoute('purchase_index');
    }
}