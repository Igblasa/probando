<?php

namespace App\Controller\BASE;

use App\Entity\intranet\IntRoles;
use App\Form\RolesType;
use App\Repository\RolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class RolesController extends AbstractController
{
    
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/rol', name: 'app_roles_index')]
    public function roles(RolesRepository $rolesRepository): Response
    {
        return $this->render('BASE/roles/index.html.twig', [
            'roles' => $rolesRepository->findAll(),
        ]);
    }
    
    #[Route('/rol/new', name: 'app_roles_new')]
    public function new(Request $request, RolesRepository $rolesRepository): Response
    {
        $role = new IntRoles();
        $form = $this->createForm(RolesType::class, $role, [
            'add' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rolesRepository->add($role, true);

            return $this->redirectToRoute('app_roles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BASE/roles/new.html.twig', [
            'role' => $role,
            'form' => $form,
        ]);
    }
    
    #[Route('/rol/show/{id}', name: 'app_roles_show')]
    public function show(IntRoles $role): Response
    {
        return $this->render('BASE/roles/show.html.twig', [
            'role' => $role,
        ]);
    }
    
    #[Route('/rol/edit/{id}', name: 'app_roles_edit')]
    public function edit(Request $request, IntRoles $role, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RolesType::class, $role);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_roles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BASE/roles/edit.html.twig', [
            'role' => $role,
            'form' => $form,
        ]);
    }
    
    #[Route('/rol/delete/{id}', name: 'app_roles_delete')]
    public function delete(Request $request, IntRoles $role, RolesRepository $rolesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$role->getId(), $request->request->get('_token'))) {
            $rolesRepository->remove($role, true);
        }

        return $this->redirectToRoute('app_roles_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/solicitar-permisos', name: 'app_user_solicitar_permisos')]
    public function solicitarPermisos(Request $request): Response
    {
        $user = $this->security->getUser();

        $form = $this->createFormBuilder()
            ->add('nombre', TextType::class, [
                'data' => $user->getUsername(),
                'attr' => ['readonly' => true, 'class' => 'form-control'],
                'label' => 'Nombre',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('email', TextType::class, [
                'data' => $user->getEmail(),
                'attr' => ['readonly' => true, 'class' => 'form-control'],
                'label' => 'Email',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('texto', TextareaType::class, [
                'attr' => ['maxlength' => 400, 'class' => 'form-control'],
                'label' => 'Texto a Enviar',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('enviar', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-3'],
                'label' => 'Enviar',
            ])
            ->getForm();

        $form->handleRequest($request);

        return $this->render('BASE/roles/solicitarPermisos.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/solicitar-permisos/gestion', name: 'app_user_gestion_solicitar_permisos')]
    public function gestionSolicitarPermisos(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->security->getUser();

        $form = $this->createFormBuilder()
            ->add('nombre', TextType::class, [
                'data' => $user->getUsername(),
                'attr' => ['readonly' => true, 'class' => 'form-control bg-light'],
                'label' => 'Nombre',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('email', TextType::class, [
                'data' => $user->getEmail(),
                'attr' => ['readonly' => true, 'class' => 'form-control bg-light'],
                'label' => 'Email',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('texto', TextareaType::class, [
                'attr' => ['maxlength' => 400, 'class' => 'form-control'],
                'label' => 'Texto a Enviar',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('enviar', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-3'],
                'label' => 'Enviar',
            ])
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            /*
            $to = 'igblasa.es';
            $subject = 'Solicitud de Permisos';
            $message = $data['texto'];
            $headers = 'From: igblasa.com' . "\r\n" .
            'Reply-To: igblasa.com' . "\r\n" . 
            'X-Mailer: PHP/' . phpversion();

            $success = mail($to, $subject, $message, $headers);
            */
            
            try {
                $data['nombre'] = mb_convert_encoding($data['nombre'], 'UTF-8', 'auto');
                $data['email'] = mb_convert_encoding($data['email'], 'UTF-8', 'auto');
                $data['texto'] = mb_convert_encoding($data['texto'], 'UTF-8', 'auto');

                $htmlContent = $this->renderView('BASE/roles/solicitarPermisosPlantilla.html.twig', array('data' => $data));

                $email = (new Email())
                    ->from('nacho@igblasa.com')
                    ->to('nacho@igblasa.com')
                    ->replyTo($data['email'])
                    ->subject('Solicitud de Permisos')
                    ->html($htmlContent)
                    ->text('Contenido alternativo en texto plano');

                $email->getHeaders()->addTextHeader('Content-Type', 'text/html; charset=UTF-8');
                $email->getHeaders()->addTextHeader('Content-Transfer-Encoding', 'quoted-printable');
                $email->getHeaders()->addTextHeader('X-Mailer', 'PHP/' . phpversion());
                $email->bcc('nacho@igblasa.com');

                $mailer->send($email);

                $this->addFlash('notice', 'Su solicitud ha sido enviada. Recibirá un email con los resultados a la mayor brevedad posible.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Hubo un problema al enviar su solicitud. Por favor, inténtelo de nuevo más tarde.');
            }

            
            return $this->redirectToRoute('app_user_solicitar_permisos');
        }
        
        return $this->redirectToRoute('app_user_solicitar_permisos');
    }
}
