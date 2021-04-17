<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController{

    // fonction calcul tva
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator ;
    }
    
    // route hello
    /**
     * @Route("/hello/{name?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($name = "World", LoggerInterface $logger){
        
        $logger->error("Mon message de log !");

        $tva = $this->calculator->calcul(100);

        dump($tva);
        
        return new Response("Hello $name !");

    }
}