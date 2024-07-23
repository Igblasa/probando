<?php

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Varios extends AbstractController
{
    public $parameters;
    
    public function __construct(ParameterBagInterface $parameters) {
        $this->parameters = $parameters;
    }
    
    public function aplicarTraducionBD($campo=null,$lang=null)
    {
        $idi = $this->parameters->get("idiomas_soportados");
        
        foreach ($idi as $k => $v){

            if($lang == $v){
                $metodo = $campo.strtoupper($lang);
                return $metodo;
            }
        }

        $metodo = $campo."ES";
        return $metodo;
    }
    
    //Valida un email usando filter_var. 
    public function validarMail($str=null)
    {
        $result = (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
        return $result;
    }
    
    public function generateToken(): string
    {
        return bin2hex(random_bytes(16)) . '_' . (new \DateTime())->format('YmdHis');
    }

}
