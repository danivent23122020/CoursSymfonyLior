<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController{
    
    // factorisation des mêmes éléments des fonctions
    protected $twig;
    public function __construct(Environment $twig)
    {
        $this->twig= $twig;
    }
    
    // route hello
    /**
     * @Route("/hello/{prenom?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($prenom = "World"){
        
            $html = $this->twig->render('hello.html.twig',
        [
            'prenom' => $prenom,
        ]
        );
            return new Response($html);
    }

    /**
     * @Route("/example", name="example")
     */
    public function example(){
        
            $html = $this->twig->render('example.html.twig',
        [
            'age' => 33,
        ]
        );
            return new Response($html);
    }
}