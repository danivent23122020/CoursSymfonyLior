<?php

namespace App\Taxes;

class Detector{
    
    public function detect(float $prix) : bool {
            if($prix > 100){
                return true;
            }
            return false;
        }
    public function charac(float $prix) : string {
            if($prix > 100){
                return "prix sup à 100";
            }
            return "prix inf à 100";
        }
    public function multipli(float $prix) : float {
            if($prix > 100){
                return $prix * 20;
            }
            return $prix * 100;
        }

}