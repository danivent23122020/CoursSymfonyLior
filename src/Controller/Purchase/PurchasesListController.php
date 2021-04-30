<?php 

namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    protected $security;
    protected $router;
    protected $twig;
    
    public function __construct(Security $security, RouterInterface $router, Environment $twig)
    {
        $this->security = $security;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @Route("/purchases", name="purchase_index")
     */
    public function index(){
        // 1 S'assurez que la personne est connectée -> avec Security
        /** @var User  */
        $user = $this->security->getUser();
        
        // sinon redirection -> homepage 
        if(!$user){
            // $url = $this->router->generate('homepage');
            // return new RedirectResponse($url);
            throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes !");
        }
        
        // 2 Qui est l'utilisateur connecté -> avec Security
        
        // 3 Passez l'utilisateur connecté à Twig pour afficher ses commandes avec Environment Twig/Response
        $html = $this->twig->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
        return new Response($html);
    }

    
}