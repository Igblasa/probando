<?php

namespace App\Controller\COLMENAR;

use App\Entity\colmenar\FutTEstadios;
use App\Form\FutTEstadiosType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FutTEstadiosController extends AbstractController
{
    private $entityManager;
    
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager('colmenar');
    }

    #[Route('/colmenar/fut-t-estadios', name: 'app_fut_t_estadios_index')]
    public function index(): Response
    {
        $futTEstadios = $this->entityManager->getRepository(FutTEstadios::class)->findAll();

        return $this->render('web_colmenar/fut_t_estadios/index.html.twig', [
            'fut_t_estadios' => $futTEstadios,
        ]);
    }

    #[Route('/colmenar/fut-t-estadios/new', name: 'app_fut_t_estadios_new')]
    public function new(Request $request): Response
    {        
        $futTEstadio = new FutTEstadios();
        $form = $this->createForm(FutTEstadiosType::class, $futTEstadio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($futTEstadio);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_estadios_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_estadios/new.html.twig', [
            'fut_t_estadio' => $futTEstadio,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-estadios/show/{id_estadio}', name: 'app_fut_t_estadios_show')]
    public function show($id_estadio): Response
    {
        $futTEstadios = $this->entityManager->getRepository(FutTEstadios::class)->findOneBy(['id_estadio' => $id_estadio]);
        
        return $this->render('web_colmenar/fut_t_estadios/show.html.twig', [
            'fut_t_estadio' => $futTEstadios,
        ]);
    }

    #[Route('/colmenar/fut-t-estadios/edit/{id_estadio}', name: 'app_fut_t_estadios_edit')]
    public function edit(Request $request, $id_estadio): Response
    {
        $futTEstadios = $this->entityManager->find(FutTEstadios::class, $id_estadio);
        
        $form = $this->createForm(FutTEstadiosType::class, $futTEstadios);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_estadios_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_estadios/edit.html.twig', [
            'fut_t_estadio' => $futTEstadios,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-estadios/delete/{id_estadio}', name: 'app_fut_t_estadios_delete')]
    public function delete(Request $request, $id_estadio): Response
    {
        $futTEstadios = $this->entityManager->find(FutTEstadios::class, $id_estadio);

        if ($this->isCsrfTokenValid('delete'.$futTEstadios->getIdEstadio(), $request->request->get('_token'))) {
            $this->entityManager->remove($futTEstadios);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_fut_t_estadios_index', [], Response::HTTP_SEE_OTHER);
    }
}
