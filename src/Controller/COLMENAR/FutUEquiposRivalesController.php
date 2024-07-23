<?php

namespace App\Controller\COLMENAR;

use App\Entity\colmenar\FutUEquiposRivales;
use App\Form\FutUEquiposRivalesType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\DatatablesAjaxPaginacion;

use App\Entity\colmenar\FutTEquipos;
use App\Entity\colmenar\FutTRivales;
use App\Entity\colmenar\FutTEstadios;
use App\Entity\colmenar\FutTDetalleTipoPartido;

class FutUEquiposRivalesController extends AbstractController
{
    public $pag;
    private $entityManager;
    
    public function __construct(ManagerRegistry $doctrine, DatatablesAjaxPaginacion $pag)
    {
        $this->pag = $pag;
        $this->entityManager = $doctrine->getManager('colmenar');
    }

    #[Route('/colmenar/fut-u-equipos-rivales', name: 'app_fut_u_equipos_rivales_index')]
    public function index(): Response
    {
        $futUEquiposRivales = $this->entityManager->getRepository(FutUEquiposRivales::class)->findAll();

        return $this->render('web_colmenar/fut_u_equipos_rivales/index.html.twig', [
            'fut_u_equipos_rivales' => $futUEquiposRivales,
        ]);
    }

    #[Route('/colmenar/fut-u-equipos-rivales-ajax', name: 'app_fut_u_equipos_rivales_index_ajax')]
    public function indexAjax(Request $request): Response
    {
        //Campos por los que quiero que aplique en where cuando se busca en la tabla.
        $columns = array(
            array( 'db' => 'equipo', 'dt' => 'equipo' ),
            array( 'db' => 'rival', 'dt' => 'rival' ),
            array( 'db' => 'nombre_estadio',  'dt' => 'nombre_estadio' ),
            array( 'db' => 'nombre_detalle', 'dt' => 'nombre_detalle' ),
            array( 'db' => 'codigo_partido', 'dt' => 'codigo_partido' ),
            array( 'db' => 'fecha', 'dt' => 'fecha' ),
            array( 'db' => 'horario', 'dt' => 'horario' ),
        );

        $connection = $this->entityManager->getConnection();
        $tabla = "fut_u_equipos_rivales";
        
        $limit = $this->pag->limit();
        $order = $this->pag->order($columns);
        $where = $this->pag->filter($columns);
        $sql = $this->pag->sql($tabla, $columns);
        
        $texto = "fut_u_equipos_rivales.id_partido, fut_u_equipos_rivales.id_equipo, fut_u_equipos_rivales.id_rival, fut_u_equipos_rivales.id_estadio, fut_u_equipos_rivales.id_detalle_tipo_partido, equipo, rival, nombre_estadio, nombre_detalle, codigo_partido, fecha, horario, resultado_local, resultado_visitante, local, observaciones, mostrar, autocar
                    FROM fut_u_equipos_rivales
                    INNER JOIN fut_t_estadios ON fut_t_estadios.id_estadio = fut_u_equipos_rivales.id_estadio
                    INNER JOIN fut_t_equipos ON fut_u_equipos_rivales.id_equipo = fut_t_equipos.id_equipo
                    INNER JOIN fut_t_rivales ON fut_u_equipos_rivales.id_rival = fut_t_rivales.id_rival
                    INNER JOIN fut_t_detalle_tipo_partido ON fut_u_equipos_rivales.id_detalle_tipo_partido = fut_t_detalle_tipo_partido.id_detalle_tipo_partido";
        
        //Metemos la consulta a mano por tener relaciones y tener que mostrar, por ejemplo, el nombre del estadio en lugar del id_estadio.
        $sql = "SELECT $texto";
        
        $sql = "$sql $where $order $limit";
        $query = $connection->executeQuery($sql);
        $results = $query->fetchAll();
        
        //Le damos la vuelta al equipo y al rival si local es no.
        foreach ($results as $key => &$registro) {
            if ($registro['local'] === 'no') {
                $temp = $registro['equipo'];
                $registro['equipo'] = $registro['rival'];
                $registro['rival'] = $temp;
            }
        }
                
        $resFilterLength = $connection->executeQuery("SELECT (SELECT COUNT(*) FROM fut_u_equipos_rivales) as total, $texto $where");
        $resFilterLength = $resFilterLength->fetchAll();
        $recordsFiltered = (is_array($resFilterLength) && count($resFilterLength) > 0) ? $resFilterLength[0]['total'] : null;

        $resTotalLength = $connection->executeQuery("SELECT (SELECT COUNT(*) FROM fut_u_equipos_rivales) as total, $texto");
        $resTotalLength = $resTotalLength->fetchAll();
        $recordsTotal = (is_array($resTotalLength) && count($resTotalLength) > 0) ? $resTotalLength[0]['total'] : null;

        $final =  array(
            "draw"            => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => $results
        );

        return $this->json($final);
    }

    #[Route('/colmenar/fut-u-equipos-rivales/new', name: 'app_fut_u_equipos_rivales_new')]
    public function new(Request $request): Response
    {
        $futUEquiposRivales = new FutUEquiposRivales();
        $futUEquiposRivales->setIdEstadio($this->entityManager->getReference(FutTEstadios::class, 28));

        $form = $this->createForm(FutUEquiposRivalesType::class, $futUEquiposRivales);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($futUEquiposRivales);
            $this->entityManager->flush();
            
            $this->addFlash('notice', 'El partido ha sido grabado correctamente');
            return $this->redirectToRoute('app_fut_u_equipos_rivales_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_u_equipos_rivales/new.html.twig', [
            'fut_u_equipos_rivales' => $futUEquiposRivales,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-u-equipos-rivales/show/{id_partido}', name: 'app_fut_u_equipos_rivales_show')]
    public function show($id_partido): Response
    {
        $futUEquiposRivales = $this->entityManager->getRepository(FutUEquiposRivales::class)->findOneBy(['id_partido' => $id_partido]);
        
        return $this->render('web_colmenar/fut_u_equipos_rivales/show.html.twig', [
            'fut_u_equipos_rivale' => $futUEquiposRivales,
        ]);
    }

    #[Route('/colmenar/fut-u-equipos-rivales/edit/{id_partido}', name: 'app_fut_u_equipos_rivales_edit')]
    public function edit(Request $request, $id_partido): Response
    {
        $futUEquiposRivales = $this->entityManager->find(FutUEquiposRivales::class, $id_partido);
        
        $futURivales = $this->entityManager->find(FutUEquiposRivales::class, $id_partido);
if (!$futUEquiposRivales) {
    throw $this->createNotFoundException('No se encontró el equipo rival');
}

// Asegúrate de que la relación con FutTEquipos esté cargada
$this->entityManager->initializeObject($futUEquiposRivales);


        
        $form = $this->createForm(FutUEquiposRivalesType::class, $futUEquiposRivales);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_fut_u_equipos_rivales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('web_colmenar/fut_u_equipos_rivales/edit.html.twig', [
            'fut_u_equipos_rivales' => $futUEquiposRivales,
            'form' => $form,
        ]);
    }

    #[Route('/colmenar/fut-u-equipos-rivales/delete/{id_partido}', name: 'app_fut_u_equipos_rivales_delete')]
    public function delete(Request $request, $id_partido): Response
    {
        $futUEquiposRivales = $this->entityManager->find(FutUEquiposRivales::class, $id_partido);
        
        if ($futUEquiposRivales) {
            $this->entityManager->remove($futUEquiposRivales);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_fut_u_equipos_rivales_index', [], Response::HTTP_SEE_OTHER);
    }
}
