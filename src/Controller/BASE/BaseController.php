<?php

namespace App\Controller\BASE;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class BaseController extends AbstractController
{
    private $urlGenerator;
    private $router;

    public function __construct(UrlGeneratorInterface $urlGenerator, UrlMatcherInterface $router)
    {
        $this->urlGenerator = $urlGenerator;
        $this->router = $router;
    }

    public function home(Request $request): Response
    {
        $user = $this->getUser();
        
        return $this->render('BASE/home.html.twig', [
            'user' => $user
        ]);
    }
 
    public function lang(Request $request, $isInitPage = false): Response
    {
        $url = empty($request->getBaseUrl()) ? $this->getParameter("url_aplicacion") : $request->getBaseUrl();
        
        $referer = $request->headers->get('referer');
        $lastPath = substr($referer, strpos($referer, $url));
        $path = str_replace($url, '', $lastPath);

        $parameters = $this->router->match($path);
        $route = $parameters['_route'];

        if (!empty($route) && !$isInitPage) {
            unset($parameters['_route']);
            return $this->redirectToRoute($route, $parameters);
        } else {
            return $this->redirectToRoute("home");
        }
    }
}
