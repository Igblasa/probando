<?php

namespace App\Controller\COLMENAR;

use App\Entity\colmenar\FutTEquipos;
use App\Form\FutTEquiposType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FutTEquiposController extends AbstractController
{
    private $entityManager;
    
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager('colmenar');
    }

    #[Route('/colmenar/fut-t-equipos', name: 'app_fut_t_equipos_index')]
    public function index(): Response
    {
        $futTEquipos = $this->entityManager->getRepository(FutTEquipos::class)->findAll();

        return $this->render('web_colmenar/fut_t_equipos/index.html.twig', [
            'fut_t_equipos' => $futTEquipos,
        ]);
    }

    #[Route('/colmenar/fut-t-equipos/new', name: 'app_fut_t_equipos_new')]
    public function new(Request $request): Response
    {        
        $futTEquipo = new FutTEquipos();
        $form = $this->createForm(FutTEquiposType::class, $futTEquipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($futTEquipo);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_equipos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_equipos/new.html.twig', [
            'fut_t_equipo' => $futTEquipo,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-equipos/show/{id_equipo}', name: 'app_fut_t_equipos_show')]
    public function show($id_equipo): Response
    {
        $futTEquipos = $this->entityManager->getRepository(FutTEquipos::class)->findOneBy(['id_equipo' => $id_equipo]);
        
        return $this->render('web_colmenar/fut_t_equipos/show.html.twig', [
            'fut_t_equipo' => $futTEquipos,
        ]);
    }

    #[Route('/colmenar/fut-t-equipos/edit/{id_equipo}', name: 'app_fut_t_equipos_edit')]
    public function edit(Request $request, $id_equipo): Response
    {
        $futTEquipos = $this->entityManager->find(FutTEquipos::class, $id_equipo);
        
        $form = $this->createForm(FutTEquiposType::class, $futTEquipos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_equipos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_equipos/edit.html.twig', [
            'fut_t_equipo' => $futTEquipos,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-equipos/delete/{id_equipo}', name: 'app_fut_t_equipos_delete')]
    public function delete(Request $request, $id_equipo): Response
    {
        $futTEquipos = $this->entityManager->find(FutTEquipos::class, $id_equipo);

        if ($this->isCsrfTokenValid('delete'.$futTEquipos->getIdEquipo(), $request->request->get('_token'))) {
            $this->entityManager->remove($futTEquipos);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_fut_t_equipos_index', [], Response::HTTP_SEE_OTHER);
    }
}
