<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController{
    
    // factorisation de Environment $twig
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
        
            return $this->render('hello.html.twig',
        [
            'prenom' => $prenom
        ]);
    }

    /**
     * @Route("/example", name="example")
     */
    public function example(){
        return $this->render('example.html.twig', 
        [
            'age' => 33
        ]);
    }

    // fonction finale dynamique
    protected function render(string $path, array $variables =[]){
        
        $html = $this->twig->render($path, $variables);
            return new Response($html);
    }
}