<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController{
    
    // route hello
    /**
     * @Route("/hello/{prenom?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($prenom = "World", Environment $twig){
        
            $html = $twig->render('hello.html.twig',
        [
            'prenom' => $prenom,
        ]
        );
            return new Response($html);
    }

    /**
     * @Route("/example", name="example")
     */
    public function example(Environment $twig){
        
            $html = $twig->render('example.html.twig',
        [
            'age' => 33,
        ]
        );
            return new Response($html);
    }
}