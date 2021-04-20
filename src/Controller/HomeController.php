<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController{
    

    /**
     * @Route("/", name="homepage")
     */
    public function homeage(){
        return $this->render('home.tml.twig');
    }
}