<?php

namespace App\Controller\COLMENAR;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Csv\Reader;
use League\Csv\CharsetConverter;
use App\Repository\COLMENAR\PartidosLigaCsvRepository;

use App\Entity\colmenar\IntPartidosData;
use App\Entity\colmenar\IntPartidosLigaCsv;
use App\Entity\colmenar\FutTEquipos;
use App\Entity\colmenar\FutTRivales;
use App\Entity\colmenar\FutTEstadios;
use App\Entity\colmenar\FutUEquiposRivales;
use App\Entity\colmenar\FutTCronicas;



class PartidosDataController extends AbstractController
{
    private $parameters;
    private $entityManager;
    
    public function __construct(ParameterBagInterface $parameters, ManagerRegistry $doctrine)
    {
        $this->parameters = $parameters;
        $this->entityManager = $doctrine->getManager('colmenar');
    }
    
    #[Route('/colmenar/partidos-data', name: 'cargar_partidos_data')]
    public function cargarDatos(Request $request): Response
    {
        $connection = $this->entityManager->getConnection('colmenar');
        
        $fechaInicio = $request->get('fechaInicio');
        $fechaFin = $request->get('fechaFin');

        // Verificamos si ambas fechas estÃƒÂ¡n presentes antes de aplicar el filtro
        $sql = "SELECT fut_u_equipos_rivales.fecha, fut_t_equipos.equipo, fut_t_rivales.rival, 
                fut_u_equipos_rivales.resultado_local, fut_u_equipos_rivales.resultado_visitante,
                fut_u_equipos_rivales.horario, fut_u_equipos_rivales.local, fut_u_equipos_rivales.id_equipo, 
                fut_u_equipos_rivales.id_estadio, fut_t_estadios.nombre_estadio, fut_t_equipos.id_tipo_equipo,
                fut_u_equipos_rivales.id_detalle_tipo_partido, fut_u_equipos_rivales.id_partido,
                fut_u_equipos_rivales.observaciones
                FROM fut_t_equipos
                INNER JOIN fut_u_equipos_rivales ON fut_u_equipos_rivales.id_equipo = fut_t_equipos.id_equipo
                INNER JOIN fut_t_rivales ON fut_u_equipos_rivales.id_rival = fut_t_rivales.id_rival
                INNER JOIN fut_t_estadios ON fut_t_estadios.id_estadio = fut_u_equipos_rivales.id_estadio";

        if ($fechaInicio && $fechaFin) {
            $sql .= " WHERE fut_u_equipos_rivales.fecha BETWEEN :fechaInicio AND :fechaFin";
        }

        $resultados = $connection->executeQuery($sql, [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ])->fetchAllAssociative();

        $this->entityManager->createQuery('DELETE FROM App\Entity\colmenar\IntPartidosData')->execute();
        // ...
        foreach ($resultados as $resultado) {
            $partidosData = new IntPartidosData();

            $fecha = new \DateTime($resultado['fecha']);
            $horario = new \DateTime($resultado['horario']);

            $partidosData->setFecha($fecha);
            $partidosData->setEquipo($resultado['equipo']);
            $partidosData->setRival($resultado['rival']);
            $partidosData->setResultadoLocal($resultado['resultado_local']);
            $partidosData->setResultadoVisitante($resultado['resultado_visitante']);
            $partidosData->setHorario($horario);
            $partidosData->setLocal($resultado['local']);
            $partidosData->setIdPartido($resultado['id_partido']);
            $partidosData->setNombreEstadio($resultado['nombre_estadio']);

            $entityManager->persist($partidosData);
        }
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Datos cargados y guardados en la entidad PartidosData.');

        return $this->redirectToRoute('ver_todos_los_partidos');
    }
    
    #[Route('/colmenar/ver-partidos-data', name: 'ver_todos_los_partidos')]
    public function verTodoLosPartidos(Request $request): Response
    {
        $this->entityManager->getRepository(IntPartidosData::class)->findUniqueEquipos();
        $this->entityManager->getRepository(IntPartidosData::class)->findUniqueRivales();

        $equiposSeleccionados = $request->get('equipo');
        $rivalesSeleccionados = $request->get('rival');
        $fechaInicio = $request->get('fechaInicio');
        $fechaFin = $request->get('fechaFin');
        $limiteRegistros = $request->get('limiteRegistros');

        if ($request->isXmlHttpRequest()) {
            $partidosFiltrados = $entityManager->getRepository(IntPartidosData::class)->findPartidosFiltrados($equiposSeleccionados, $rivalesSeleccionados, $fechaInicio, $fechaFin, $limiteRegistros);
            return $this->json($partidosFiltrados);
        }

        $partidosFiltrados = $this->entityManager->getRepository(IntPartidosData::class)->findPartidosFiltrados($equiposSeleccionados, $rivalesSeleccionados, $fechaInicio, $fechaFin, $limiteRegistros);

        return $this->render('COLMENAR/partidos/ver_cargar_datos.html.twig', [
            'equipos' => '',
            'rivales' => '',
            'partidos' => $partidosFiltrados,
        ]);
    }
    
    #[Route('/colmenar/ver-cargar-partidos-liga', name: 'ver_cargar_partidos_liga')]
    public function verCargarPartidosLiga(Request $request, PartidosLigaCsvRepository $PartidosLigaCsvRepository): Response
    {
        $partidosCsv = $this->entityManager->getRepository(IntPartidosLigaCsv::class)->findAll();
        
        return $this->render('COLMENAR/partidos/ver_cargar_partidos_liga.html.twig', [
            'partidosCsv' => $partidosCsv,
        ]);
    }
    
    #[Route('/colmenar/grabar-partidos-liga', name: 'grabar_partidos_liga')]
    public function grabarPartidosLiga(Request $request, PartidosLigaCsvRepository $PartidosLigaCsvRepository): Response
    {
        $partidosCsv = $this->entityManager->getRepository(IntPartidosData::class)->findAll();

        foreach ($partidosCsv as $partidoCsv) {
            $local = $PartidosLigaCsvRepository->getLocal($partidoCsv);
            $codigo_partido = $PartidosLigaCsvRepository->getCodigoPartido($partidoCsv);
            $competicion = $PartidosLigaCsvRepository->getCompeticionTalCual($partidoCsv);
            $grupo = $PartidosLigaCsvRepository->getGrupo($partidoCsv);
            $jornada = $PartidosLigaCsvRepository->getJornada($partidoCsv);

            $id_equipo = $PartidosLigaCsvRepository->getIdEquipo($partidoCsv, $this->entityManager, $local);
            if (!$id_equipo) {
                $this->addFlash('error', 'El partido con código ' . $codigo_partido . ' no ha sido grabado por haber un error con el id_equipo');
                continue;
            }
            $equipo = $this->entityManager->getRepository(FutTEquipos::class)->find($id_equipo);

            $id_rival = $PartidosLigaCsvRepository->getIdRival($partidoCsv, $this->entityManager, $local);
            if (!$id_rival) {
                $this->addFlash('error', 'El partido con código ' . $codigo_partido . ' no ha sido grabado por haber un error con el id_rival');
                continue;
            }
            $rival = $this->entityManager->getRepository(FutTRivales::class)->find($id_rival);

            $estadio = $PartidosLigaCsvRepository->getIdEstadio($partidoCsv, $this->entityManager, $id_rival);
            $detalle_tipo_partido = $PartidosLigaCsvRepository->getIdDetallePartido($partidoCsv, $this->entityManager);
            $fecha = $PartidosLigaCsvRepository->getFecha($partidoCsv);
            $horario = $PartidosLigaCsvRepository->getHora($partidoCsv, $id_rival);
            $resultado_local = $PartidosLigaCsvRepository->getResultado($partidoCsv, 0);
            $resultado_visitante = $PartidosLigaCsvRepository->getResultado($partidoCsv, 1);

            $futUEquiposRivales = $this->entityManager->getRepository(FutUEquiposRivales::class)->findOneBy(['codigoPartido' => $codigo_partido]);
            $futUEquiposRivales = $futUEquiposRivales ?: new FutUEquiposRivales();

            $futUEquiposRivales->setIdEquipo($equipo);
            $futUEquiposRivales->setIdRival($rival);
            $futUEquiposRivales->setIdEstadio($estadio);
            $futUEquiposRivales->setIdDetalleTipoPartido($detalle_tipo_partido);
            $futUEquiposRivales->setFecha($fecha);
            $futUEquiposRivales->setHorario($horario);
            $futUEquiposRivales->setLocal($local);
            $futUEquiposRivales->setMostrar(1);
            $futUEquiposRivales->setResultadoLocal($resultado_local);
            $futUEquiposRivales->setResultadoVisitante($resultado_visitante);
            $futUEquiposRivales->setCodigoPartido($codigo_partido);
            $futUEquiposRivales->setCompeticion($competicion);
            $futUEquiposRivales->setGrupo($grupo);
            $futUEquiposRivales->setJornada($jornada);

            $this->entityManager->persist($futUEquiposRivales);
            $this->entityManager->flush();

            $this->addFlash('notice', 'El partido con código ' . $codigo_partido . ' ha sido grabado correctamente');

            // Insertar en FutTCronicas si no existe
            $cronicaExistente = $this->entityManager->getRepository(FutTCronicas::class)->findOneBy(['idPartido' => $futUEquiposRivales->getIdPartido()]);
            if (!$cronicaExistente) {
                $cronica = new FutTCronicas();
                $cronica->setIdPartido($futUEquiposRivales);
                $cronica->setPublicadoEnRedes('No');
                $cronica->setEnviadoWhatsapp('No');
                $this->entityManager->persist($cronica);
                $this->entityManager->flush();
            }

            $partidoCsvEntity = $PartidosLigaCsvRepository->findOneBy(['codigoPartido' => $codigo_partido]);
            if ($partidoCsvEntity) {
                $this->entityManager->remove($partidoCsvEntity);
                $this->entityManager->flush();
            }

            if ($partidoCsv->getClubVisitante() == 1031 && $partidoCsv->getClubCasa() == 1031) {
                $codigo_partido = $codigo_partido + 100000000;
                $futUEquiposRivalesBis = $this->entityManager->getRepository(FutUEquiposRivales::class)->findOneBy(['codigoPartido' => $codigo_partido]);
                $futUEquiposRivalesBis = $futUEquiposRivalesBis ?: new FutUEquiposRivales();

                $id_equipo = $PartidosLigaCsvRepository->getIdEquipo($partidoCsv, $this->entityManager, 'no');
                $equipo = $this->entityManager->getRepository(FutTEquipos::class)->find($id_equipo);
                $id_rival = $PartidosLigaCsvRepository->getIdRivalComoLocal($partidoCsv, $this->entityManager, 'no');
                $rival = $this->entityManager->getRepository(FutTRivales::class)->find($id_rival);

                $futUEquiposRivalesBis->setIdEquipo($equipo);
                $futUEquiposRivalesBis->setIdRival($rival);
                $futUEquiposRivalesBis->setIdEstadio($estadio);
                $futUEquiposRivalesBis->setIdDetalleTipoPartido($detalle_tipo_partido);
                $futUEquiposRivalesBis->setFecha($fecha);
                $futUEquiposRivalesBis->setHorario($horario);
                $local = ($local == 'si') ? 'no' : 'si';
                $futUEquiposRivalesBis->setLocal($local);
                $futUEquiposRivalesBis->setMostrar(0);
                $futUEquiposRivalesBis->setResultadoLocal($resultado_local);
                $futUEquiposRivalesBis->setResultadoVisitante($resultado_visitante);
                $futUEquiposRivalesBis->setCodigoPartido($codigo_partido);
                $futUEquiposRivalesBis->setCompeticion($competicion);
                $futUEquiposRivalesBis->setGrupo($grupo);
                $futUEquiposRivalesBis->setJornada($jornada);

                $this->entityManager->persist($futUEquiposRivalesBis);
                $this->entityManager->flush();

                // Insertar en FutTCronicas si no existe
                $cronicaExistenteBis = $this->entityManager->getRepository(FutTCronicas::class)->findOneBy(['idPartido' => $futUEquiposRivalesBis->getIdPartido()]);
                if (!$cronicaExistenteBis) {
                    $cronicaBis = new FutTCronicas();
                    $cronicaBis->setIdPartido($futUEquiposRivalesBis);
                    $cronica->setPublicadoEnRedes('No');
                    $cronica->setEnviadoWhatsapp('No');
                    $this->entityManager->persist($cronicaBis);
                    $this->entityManager->flush();
                }
            }
        }

        return $this->redirectToRoute('ver_cargar_partidos_liga');
    }

    
    #[Route('/colmenar/cargar-partidos-csv', name: 'cargar_partidos_csv')]
    public function cargarPartidosCsv(Request $request): Response
    {
        $file = $request->files->get('csv_file');

        if ($file) {
            $this->entityManager->createQuery('DELETE FROM App\Entity\colmenar\IntPartidosLigaCsv')->execute();

            // Crear un objeto CsvReader
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setDelimiter(';');
            CharsetConverter::addTo($csv, 'ISO-8859-1', 'UTF-8');

            $records = $csv->getRecords();

            foreach ($records as $key => $record) {
                
                if ($key === 0) {
                    continue;
                }

                $entidad = new IntPartidosLigaCsv();
                $entidad->setFecha(\DateTime::createFromFormat('d-m-Y', $record[0]));
                
                $hora = empty($record[1]) ? null : new \DateTime($record[1]);
                $entidad->setHora($hora);
                $entidad->setCompeticion($record[2]);
                $entidad->setGrupo($record[3]);
                $entidad->setClubCasa((int)$record[4]);
                $entidad->setClubVisitante((int)$record[5]);
                $entidad->setNombreClubCasa($record[6]);
                $entidad->setNombreClubVisitante($record[7]);
                $entidad->setEquipoCasa($record[8]);
                $entidad->setEquipoVisitante($record[9]);
                $entidad->setCampo($record[10]);
                $entidad->setDireccionCampo($record[11]);
                $entidad->setJornada($record[12]);
                $entidad->setResultado($record[13]);
                $entidad->setCodigoPartido((int)$record[14]);
                $entidad->setArbitro($record[15]);

                $this->entityManager->persist($entidad);
            }

            $this->entityManager->flush();

            $this->addFlash('notice', 'Datos cargados correctamente.');
            return $this->redirectToRoute('ver_cargar_partidos_liga');
        }
        
        $this->addFlash('warning', 'El archivo no ha sido cargado.');
        return $this->redirectToRoute('ver_cargar_partidos_liga');
    }
    
    #[Route('/colmenar/eliminar-partidos-csv', name: 'eliminar_partidos_csv')]
    public function eliminarPartidosCsv(Request $request): Response
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\colmenar\IntPartidosLigaCsv')->execute();
        $this->addFlash('notice', 'Datos eliminados correctamente.');
        
        return $this->redirectToRoute('ver_cargar_partidos_liga');
    }
}