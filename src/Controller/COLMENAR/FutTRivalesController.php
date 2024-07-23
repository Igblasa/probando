<?php

namespace App\Controller\COLMENAR;

use App\Entity\colmenar\FutTRivales;
use App\Form\FutTRivalesType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\COLMENAR\FutTRivalesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\DatatablesAjaxPaginacion;

class FutTRivalesController extends AbstractController
{
    public $pag;
    private $entityManager;
    
    public function __construct(ManagerRegistry $doctrine, DatatablesAjaxPaginacion $pag)
    {
        $this->pag = $pag;
        $this->entityManager = $doctrine->getManager('colmenar');
    }

    #[Route('/colmenar/fut-t-rivales', name: 'app_fut_t_rivales_index')]
    public function index(): Response
    {
        $futTRivales = $this->entityManager->getRepository(FutTRivales::class)->findBy([], ['rival' => 'ASC']);

        return $this->render('web_colmenar/fut_t_rivales/index.html.twig', [
            'fut_t_rivales' => $futTRivales,
        ]);
    }

    #[Route('/colmenar/fut-t-rivales-ajax', name: 'app_fut_t_rivales_index_ajax')]
    public function indexAjax(Request $request, FutTRivalesRepository $futTRivalesRepository): Response
    {
        $metadata = $this->entityManager->getClassMetadata('App\Entity\colmenar\FutTRivales');
        $columnMappings = $metadata->getColumnNames();

        $columns = [];
        foreach ($columnMappings as $columnName) {
            $columns[] = ['db' => $columnName, 'dt' => $columnName];
        }
        
        $connection = $this->entityManager->getConnection();
        $tabla = "fut_t_rivales";

        $limit = $this->pag->limit();
        $order = $this->pag->order($columns);
        $where = $this->pag->filter($columns);

        $sql = $this->pag->sql($tabla, $columns);
        $sql = "$sql $where $order $limit";

        // Preparar y ejecutar la consulta principal
        $stmt = $connection->prepare($sql);
        $query = $stmt->executeQuery();
        $results = $query->fetchAllAssociative();

        // Preparar y ejecutar la consulta para el total filtrado
        $countSqlFiltered = "SELECT COUNT(*) as total FROM $tabla $where";
        $stmtFiltered = $connection->prepare($countSqlFiltered);
        $resFilterLength = $stmtFiltered->executeQuery();
        $recordsFiltered = $resFilterLength->fetchOne();

        // Preparar y ejecutar la consulta para el total general
        $countSqlTotal = "SELECT COUNT(*) as total FROM $tabla";
        $stmtTotal = $connection->prepare($countSqlTotal);
        $resTotalLength = $stmtTotal->executeQuery();
        $recordsTotal = $resTotalLength->fetchOne();

        $final =  array(
                "draw"            => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
                "recordsTotal"    => intval($recordsTotal),
                "recordsFiltered" => intval($recordsFiltered),
                "data"            => $results
        );

        return $this->json($final);
    }

    #[Route('/colmenar/fut-t-rivales/new', name: 'app_fut_t_rivales_new')]
    public function new(Request $request): Response
    {       
        $futTRivales = new FutTRivales();
        $form = $this->createForm(FutTRivalesType::class, $futTRivales);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($futTRivales);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_rivales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_rivales/new.html.twig', [
            'fut_t_rivale' => $futTRivales,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-rivales/show/{id_rival}', name: 'app_fut_t_rivales_show')]
    public function show($id_rival): Response
    {
        $futTRivales = $this->entityManager->getRepository(FutTRivales::class)->findOneBy(['id_rival' => $id_rival]);
        
        return $this->render('web_colmenar/fut_t_rivales/show.html.twig', [
            'fut_t_rivale' => $futTRivales,
        ]);
    }

    #[Route('/colmenar/fut-t-rivales/edit/{id_rival}', name: 'app_fut_t_rivales_edit')]
    public function edit(Request $request, $id_rival): Response
    {
        $futTRivales = $this->entityManager->find(FutTRivales::class, $id_rival);
        
        $form = $this->createForm(FutTRivalesType::class, $futTRivales);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            
            $otherRecords = $this->entityManager->getRepository(FutTRivales::class)->findBy(['codEqFed' => $futTRivales->getCodEqFed()]);

            foreach ($otherRecords as $record) {
                // Actualizar campos camiseta, pantalon y medias
                $record->setCamiseta($futTRivales->getCamiseta());
                $record->setPantalon($futTRivales->getPantalon());
                $record->setMedias($futTRivales->getMedias());
                // Actualizar comprobada_equipacion
                $record->setComprobadaEquipacion($futTRivales->getComprobadaEquipacion());
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_t_rivales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_t_rivales/edit.html.twig', [
            'fut_t_rivale' => $futTRivales,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-t-rivales/delete/{id_rival}', name: 'app_fut_t_rivales_delete')]
    public function delete(Request $request, $id_rival): Response
    {
        $futTRivales = $this->entityManager->find(FutTRivales::class, $id_rival);
        
        if ($futTRivales) {
            $this->entityManager->remove($futTRivales);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_fut_t_rivales_index', [], Response::HTTP_SEE_OTHER);
    }
}
