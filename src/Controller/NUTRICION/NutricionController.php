<?php

namespace App\Controller\NUTRICION;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NutricionController extends AbstractController
{
    
    #[Route('/nutricion/home', name: 'ruta_nutricion')]
    public function home(Request $request): Response
    {
        return $this->render('NUTRICION/home.html.twig', [
            'hola' => 'hola',
        ]);
    }
    
}
