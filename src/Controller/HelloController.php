<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use App\Taxes\Detector;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController{

    // route hello
    /**
     * @Route("/hello/{name?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($name = "World", LoggerInterface $logger, Calculator $calculator, Slugify $slugify, Environment $twig, Detector $detector){

        // exercice 01 chapitre le container de service
        dump($detector->detect(101));
        dump($detector->detect(10));
        
        
        // nécessaire pour formation -> twig
        dump($twig);
        // test pour formation -> slugify
        dump($slugify->slugify("Hello World"));
        
        $logger->error("Mon message de log !");

        $tva = $calculator->calcul(100);

        dump($tva);
        
        return new Response("Hello $name !");

    }
}