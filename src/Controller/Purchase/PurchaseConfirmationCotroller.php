<?php 

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PurchaseConfirmationCotroller
{
    protected $formFactory;
    protected $router;
    protected $security;
    protected $cartService;
    protected $em;
    
    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router , Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request, FlashBagInterface $flashBag)
    {
        // 1 nous voulons lire les données du formulaire -> FormFactoryInterface & Request
        $form = $this->formFactory->create(CartConfirmationType::class);
        $form->handleRequest($request);
        
        // 2 si le formulaire n'a pas été soumis -> dégager
        if(!$form->isSubmitted()){
            // message Flash puis redirection -> FlashBagInterface
            $flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');
            return new RedirectResponse($this->router->generate('cart_show'));
        }
        
        // 3 si je ne suis pas connecté -> dégager -> Security
        $user = $this->security->getUser();
        if(!$user){
            throw new AccessDeniedException("vous devez être connecté pour confirmer une commande");
        }
        
        // 4 si il n'y a pas de produits dans mon panier -> dégager -> CartService
        $cartItems = $this->cartService->getDetailedCartItems();
        if(count($cartItems) === 0){
            $flashBag->add('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return new RedirectResponse($this->router->generate('cart_show'));
        }

        // 5 nous allons créer une Purchase
        /** @var Purchase */
        $purchase = $form->getData();
        
        // 6 nous allons la lier avec l'utilisateur actuellement connecté -> Security
        $purchase->setUser($user)
        ->setPurchasedAt(new DateTime());
        
        $this->em->persist($purchase);
        
        // 7 nous allons la lier avec les produits qui sont dans le panier -> CartService
        $total = 0;
        foreach($this->cartService->getDetailedCartItems() as $cartItem){
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
            ->setProduct($cartItem->product)
            ->setProductName($cartItem->product->getPrice())
            ->setQuantity($cartItem->qty)
            ->setTotal($cartItem->getTotal())
            ->setProductPrice($cartItem->product->getPrice());

            $total += $cartItem->getTotal();

            $this->em->persist($purchaseItem);
        }

        $purchase->setTotal($total);
        
        // 8 nous allons enregistrer la commande -> EntityManagerInterface
        $this->em->flush();
        
        // message de d'enregistrement
        $flashBag->add('success', 'La commande a bien été enregistrée' );
        // redirection 
        return new RedirectResponse($this->router->generate('purchase_index'));
    }
}