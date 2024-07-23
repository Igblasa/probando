<?php

namespace App\Controller\COLMENAR;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

class ColmenarController extends AbstractController
{
    private $parameters;
    
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }
}