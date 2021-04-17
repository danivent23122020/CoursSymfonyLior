<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController{
 
    // route hello
    /**
     * @Route("/hello/{name?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($name = "World", LoggerInterface $logger, Calculator $calculator){
        
        $logger->error("Mon message de log !");

        $tva = $calculator->calcul(100);

        dump($tva);
        
        return new Response("Hello $name !");

    }
}