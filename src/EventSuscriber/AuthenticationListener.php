<?php

namespace App\EventSuscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Controller\SIID\InsideStoredProcedureController;


/*
 * KernelEvents::CONTROLLER => 'datosUsuario'
 * Antes de ejecutar el controller y todas las llamadas, comprobamos si están los datos del usuario.
 * Si no lo están, llamamos al procedimiento spGetUsuario para conseguirlos y guardarlos en sesion
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 */

class AuthenticationListener implements EventSubscriberInterface
{
    public $tokenStorage;
    public $stored;
    public $user;
    public $entityManager;
    public $security;
    
    public function __construct(TokenStorageInterface $TokenStorageInterface, EntityManagerInterface $entityManager, Security $security){
        
        $this->tokenStorage = $TokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }
    
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser() instanceof UserInterface) {
            return;
        }

        // Verificar si el usuario tiene el rol ROLE_GOOGLE
        $user = $token->getUser();
        if ($this->hasGoogleRole($user)) {
            $googleAccessToken = $user->getGoogleAccessToken();
            $googleAccessTokenExpiresAt = $user->getGoogleTokenExpiration();

            if ($googleAccessToken && $googleAccessTokenExpiresAt instanceof \DateTimeInterface) {
                $now = new \DateTime();
                if ($googleAccessTokenExpiresAt < $now) {
                    // El token de acceso ha expirado, quitar el rol ROLE_GOOGLE
                    $this->removeGoogleRole($user);
                }
            } else {
                // No se encontró el token de acceso de Google o la fecha de expiración en la base de datos
                // Quitar el rol ROLE_GOOGLE
                $this->removeGoogleRole($user);
            }
        }
    }

    private function hasGoogleRole(UserInterface $user): bool
    {
        return in_array('ROLE_GOOGLE', $user->getRoles());
    }

    private function removeGoogleRole(UserInterface $user): void
    {
        $user->setRoles(array_diff($user->getRoles(), ['ROLE_GOOGLE']));
        $this->entityManager->flush();
        $this->tokenStorage->setToken(new \Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken($user, 'main', $user->getRoles()));
    }
    
    
    
}