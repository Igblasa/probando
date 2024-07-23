<?php

namespace App\Controller\COLMENAR;

use App\Entity\colmenar\FutTEmpresas;
use App\Form\FutTEmpresasType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FutTEmpresasController extends AbstractController
{
    private $entityManager;
    
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager('colmenar');
    }

    #[Route('/colmenar/fut-t-empresas', name: 'app_fut_t_empresas_index')]
    public function index(): Response
    {
        $futTEmpresas = $this->entityManager->getRepository(FutTEmpresas::class)->findAll();

        return $this->render('web_colmenar/fut_t_empresas/index.html.twig', [
            'fut_t_empresas' => $futTEmpresas,
        ]);
    }

    #[Route('/colmenar/fut-t-empresas/new', name: 'app_fut_t_empresas_new')]
    public function new(Request $request): Response
    {        
        $futTEmpresa = new FutTEmpresas();
        $form = $this->createForm(FutTEmpresasType::class, $futTEmpresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($futTEmpresa);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_empresas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_empresas/new.html.twig', [
            'fut_t_empresa' => $futTEmpresa,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-empresas/show/{id_empresa}', name: 'app_fut_t_empresas_show')]
    public function show($id_empresa): Response
    {
        $futTEmpresas = $this->entityManager->getRepository(FutTEmpresas::class)->findOneBy(['id_empresa' => $id_empresa]);
        
        return $this->render('web_colmenar/fut_t_empresas/show.html.twig', [
            'fut_t_empresa' => $futTEmpresas,
        ]);
    }

    #[Route('/colmenar/fut-t-empresas/edit/{id_empresa}', name: 'app_fut_t_empresas_edit')]
    public function edit(Request $request, $id_empresa): Response
    {
        $futTEmpresas = $this->entityManager->find(FutTEmpresas::class, $id_empresa);
        
        $form = $this->createForm(FutTEmpresasType::class, $futTEmpresas);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_empresas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_empresas/edit.html.twig', [
            'fut_t_empresa' => $futTEmpresas,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-empresas/delete/{id_empresa}', name: 'app_fut_t_empresas_delete')]
    public function delete(Request $request, $id_empresa): Response
    {
        $futTEmpresas = $this->entityManager->find(FutTEmpresas::class, $id_empresa);

        if ($this->isCsrfTokenValid('delete'.$futTEmpresas->getIdEmpresa(), $request->request->get('_token'))) {
            $this->entityManager->remove($futTEmpresas);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_fut_t_empresas_index', [], Response::HTTP_SEE_OTHER);
    }
}
