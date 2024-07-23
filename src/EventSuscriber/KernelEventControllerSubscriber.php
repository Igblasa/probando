<?php

namespace App\EventSuscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Controller\SIID\InsideStoredProcedureController;


/*
 * KernelEvents::CONTROLLER => 'datosUsuario'
 * Antes de ejecutar el controller y todas las llamadas, comprobamos si están los datos del usuario.
 * Si no lo están, llamamos al procedimiento spGetUsuario para conseguirlos y guardarlos en sesion
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 */

class KernelEventControllerSubscriber implements EventSubscriberInterface
{
    public $tokenStorage;
    public $stored;
    public $session;
    public $user;
    public $registry;
    
    public function __construct(RequestStack $session,
                                TokenStorageInterface $TokenStorageInterface,
                                \Doctrine\Persistence\ManagerRegistry $registry){
        
        $this->tokenStorage = $TokenStorageInterface;
        $this->session = $session->getSession();
        $this->registry = $registry;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'prueba'
        );
	
    }
    
    public function prueba()
    {
        return;
        
    }
}