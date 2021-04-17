<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Detector{

    // protected $logger;
    // protected $tva;
    protected $amount;
    
    public function __construct(LoggerInterface $detect, int $amount)
    {
        $this->detect = $detect;
        $this->amount =$amount;
    }
    
    // public function __construct(LoggerInterface $logger, float $tva)
    // {
    //     $this->logger =$logger;
    //     $this->tva =$tva;
    // }

    
    public function calcul(float $prix) : float {
        $this->logger->info("Un calcul a lieu : $prix");
        return $prix * (20/100);
    }
}