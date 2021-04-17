<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController{

    // fonction calcul tva
    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }
    
    // route index
    /**
     * @Route("/", name="index")
     */
    public function index(){
        $tva = $this->calculator->calcul(100);
        dump($tva);
        dump("Ça fonctionne");
        die();
    }
    // route test
    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"})
     */
    public function test(Request $req, $age){

        return new Response("Vous avez $age ans !");

    }
}