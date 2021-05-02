<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    protected $session;
    protected $productRepository;
    
    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }
    
    // factorisation de ('cart', [])
    protected function getCart() : array
    {
        return $this->session->get('cart', []);
    }
    
    // factorisation de ('cart', $cart)
    protected function saveCart(array $cart) 
    {
        $this->session->set('cart', $cart);
    }

    // ========================
    // fonction vider le panier
    public function empty()
    {
        $this->saveCart([]);
    }
    
    // ================================================
    // fonction en charge de la mise en forme du panier
    public function add(int $id){
        
        // 1.   Retrouver le panier dans la session (sous forme de tableau)
        // 2.	S’il n’existe pas encore, alors prendre un tableau vide
        
        $cart = $this->getCart();
        
        // 3.	Voir si le produit ($id) existe dans le tableau
        // 4.	Si c’est le cas, simplement augmenter la quantité
        // 5.	Sinon, ajouter le produit avec la quantité 1
        
        if(!array_key_exists($id, $cart)){
            $cart[$id] = 0;
        } 
        $cart[$id]++;
        
        
        // 6.	Enregistrer le panier/tableau mis à jour dans la session
        $this->saveCart($cart);
    }
    
    
    // ==========================================
    // fonction en charge du delete product panier
    public function remove(int $id){
        $cart =$this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    // ==========================================
    // fonction en charge de décrémenter le nb de product du panier
    public function decrement(int $id){
        $cart =$this->getCart();

        if(!array_key_exists($id, $cart)){
            return;
        }
        // soit le nd de produit est à 1
        // alors il faut le supprimer
        if($cart[$id] === 1){
            $this->remove($id);
            return;
        }
        
        // soit le nd de produit est à plus de 1
        // alors il faut le décrémenter
        $cart[$id]--;

        $this->saveCart($cart);
    }
    
    // ==========================================================
    // fonction en charge du prix total du produit par sa quantité
    public function getTotal() : int
    {
        $total = 0;

        foreach($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);

            // sécurité si produit n'existe plus dans BDD
            if(!$product){
                continue;
            }
            
            $total += $product->getPrice() * $qty;
        }
        
        return $total;
    } 
    
    
    // ==========================================
    // fonction en charge du prix total du panier
    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems() : array
    {
        $detailedCart = [];
        
        foreach($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);

            // sécurité si produit n'existe plus dans BDD
            if(!$product){
                continue;
            }
            
            $detailedCart[] = new CartItem($product, $qty);
        }
        
        return $detailedCart;
    } 
}