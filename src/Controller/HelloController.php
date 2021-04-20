<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController extends AbstractController
{   
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

    // route example
    /**
     * @Route("/example", name="example")
     */
    public function example(){
        return $this->render('example.html.twig', 
        [
            'age' => 33
        ]);
    }
}