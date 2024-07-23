<?php

namespace App\Controller\BASE;

use App\Entity\intranet\IntUser;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\KernelInterface;

class UserController extends AbstractController
{
    private $projectDir;
    
    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir(); // Obtiene el directorio base del proyecto
    }
    
    #[Route('/user', name: 'app_user_index')]
    public function user(UserRepository $userRepository): Response
    {
        return $this->render('BASE/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    #[Route('/user/new', name: 'app_user_new')]
    public function new(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new IntUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Codificar la contraseña utilizando bcrypt
            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BASE/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/show/{id}', name: 'app_user_show')]
    public function show(IntUser $user): Response
    {
        return $this->render('BASE/user/show.html.twig', [
            'user' => $user,
        ]);
    }
    
    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function edit(Request $request, IntUser $user, UserRepository $userRepository): Response
    {
        $roleData = Yaml::parseFile($this->projectDir . '/config/packages/security.yaml');
        $definedRoles = $roleData['security']['role_hierarchy'];

        $rolesChoices = [];
        foreach ($definedRoles as $role => $inherits) {
            $rolesChoices[$role] = $role;
        }
        
        
        $form = $this->createForm(UserType::class, $user, [
            'role_choices' => $rolesChoices,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BASE/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    
    #[Route('/user/delete/{id}', name: 'app_user_delete')]
    public function delete(Request $request, IntUser $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
