<?php

namespace App\Controller\BASE;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Google_Service_Oauth2;
use Google\Client;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\intranet\IntUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends AbstractController
{
    private $tokenStorage;
    private $entityManager;
    private $security;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function login(AuthenticationUtils $authUtils, UrlGeneratorInterface $urlGenerator)
    {
        $token = $this->tokenStorage->getToken();

        if ($token && $token->getUser() instanceof IntUser) {
            return $this->redirectToRoute('home');
        }

        $error = $authUtils->getLastAuthenticationError();

        if($error){
            $this->addFlash('error',$error->getMessage());
        }

        $lastUsername = $authUtils->getLastUsername();

        return $this->render('BASE/security/login.html.twig', array(
            'mostrar_menu' => false,
            'last_username' => $lastUsername,
        ));
    }

    public function loginGoogle(UrlGeneratorInterface $urlGenerator)
    {
        try {
            $client = new Client();
            $client->setClientId($this->getParameter("clientIDGoogle"));
            $client->setClientSecret($this->getParameter("clientSecretGoogle"));
            $client->setRedirectUri($urlGenerator->generate('google_login_check', [], UrlGeneratorInterface::ABSOLUTE_URL));
            $client->addScope('email');
            $client->addScope('profile');

            $authUrl = $client->createAuthUrl();
            return new RedirectResponse($authUrl);
        } catch (\Exception $e) {
            $this->addFlash('error', 'No se pudo obtener la URL de autenticación de Google. Por favor, inténtalo de nuevo más tarde.');
            return $this->redirectToRoute('login');
        }
    }

    public function googleLoginCheck(Request $request, TokenStorageInterface $tokenStorage)
    {
        $googleCode = $request->query->get('code');
        if (!$googleCode) {
            return $this->redirectToRoute('login');
        }

        $client = new Client();
        $client->setClientId($this->getParameter("clientIDGoogle"));
        $client->setClientSecret($this->getParameter("clientSecretGoogle"));
        $client->setRedirectUri($this->generateUrl('google_login_check', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $client->addScope('email');
        $client->addScope('profile');

        try {
            $googleAccessToken = $client->fetchAccessTokenWithAuthCode($googleCode);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('login');
        }
        
        $datosDeGoogle = $this->getUserEmailFromGoogleAccessToken($googleAccessToken);

        if($datosDeGoogle->getVerifiedEmail() !== true){
            $this->addFlash('error', 'Usuario no verificado en Google');
            return $this->redirectToRoute('login');
        }

        if ($datosDeGoogle) {
            $userRepository = $this->entityManager->getRepository(IntUser::class);
            $user = $userRepository->findOneBy(['email' => $datosDeGoogle->getEmail()]);

            if ($user) {
                $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

                // Coloca el token en el almacenamiento de tokens
                $tokenStorage->setToken($token);
                
                // Almacena el token de acceso en la sesión
                $session = $request->getSession();
                $session->set('google_access_token', $googleAccessToken['access_token']);

                return $this->redirectToRoute('home');
            } else {
                $user = new IntUser();
                $user->setUsername($datosDeGoogle->getName());
                $user->setEmail($datosDeGoogle->getEmail());
                $user->setImagen($datosDeGoogle->getPicture());
                $user->setRoles(['ROLE_USER']);
                $user->setPassword('');
                $user->setIsActive(true);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
                $tokenStorage->setToken($token);
                
                $session = $request->getSession();
                $session->set('google_access_token', $googleAccessToken['access_token']);

                return $this->redirectToRoute('home');
            }
        } else {
            return $this->redirectToRoute('login');
        }
    }

    private function getUserEmailFromGoogleAccessToken(array $googleAccessToken)
    {
        $client = new Client();
        $client->setAccessToken($googleAccessToken);

        if ($client->isAccessTokenExpired()) {
            return null;
        }

        $googleService = new Google_Service_Oauth2($client);
        $userData = $googleService->userinfo->get();
        return $userData;
    }
}
