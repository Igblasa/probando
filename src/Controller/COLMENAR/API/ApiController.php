<?php

namespace App\Controller\COLMENAR\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\colmenar\FutTCronicas;
use App\Entity\colmenar\FutUEquiposRivales;

class ApiController extends AbstractController
{
    private $tokenStorage;
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager,) {
        $this->entityManager = $entityManager;
    }

    /**
    * @Route("/api/partidos", name="api_partidos")
    */
    public function getFutTPartidos(Request $request): Response
    {
        $apiKey = $request->headers->get('Api-Key-getFutTPartidos');
            $validApiKey = 'a1b2c3d4e5f67890abcd1234567890abcdef1234567890abcdef1234567890';
        
        if ($apiKey !== $validApiKey) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Obtener los parámetros de la solicitud
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');
        $equipo = $request->query->get('equipo');
        $rival = $request->query->get('rival');
        $estadio = $request->query->get('estadio');

        // Construir la consulta SQL con los parámetros
        $sql = "
            SELECT fut_u_equipos_rivales.fecha, fut_t_equipos.equipo, fut_t_rivales.rival, 
                   fut_u_equipos_rivales.resultado_local, fut_u_equipos_rivales.resultado_visitante,
                   fut_u_equipos_rivales.horario, fut_u_equipos_rivales.local, fut_u_equipos_rivales.id_equipo, 
                   fut_u_equipos_rivales.id_estadio, fut_t_estadios.nombre_estadio, fut_t_equipos.id_tipo_equipo,
                   fut_u_equipos_rivales.id_detalle_tipo_partido, fut_u_equipos_rivales.id_partido,
                   fut_u_equipos_rivales.observaciones
            FROM fut_t_equipos
            INNER JOIN fut_u_equipos_rivales ON fut_u_equipos_rivales.id_equipo = fut_t_equipos.id_equipo
            INNER JOIN fut_t_rivales ON fut_u_equipos_rivales.id_rival = fut_t_rivales.id_rival
            INNER JOIN fut_t_estadios ON fut_t_estadios.id_estadio = fut_u_equipos_rivales.id_estadio
        ";

        // Añadir condiciones de filtro si están presentes
        $conditions = [];
        $parameters = [];

        if ($fechaInicio) {
            $conditions[] = 'fut_u_equipos_rivales.fecha >= :fechaInicio';
            $parameters['fechaInicio'] = $fechaInicio;
        }

        if ($fechaFin) {
            $conditions[] = 'fut_u_equipos_rivales.fecha <= :fechaFin';
            $parameters['fechaFin'] = $fechaFin;
        }

        if ($equipo) {
            $conditions[] = 'fut_t_equipos.equipo = :equipo';
            $parameters['equipo'] = $equipo;
        }

        if ($rival) {
            $conditions[] = 'fut_t_rivales.rival = :rival';
            $parameters['rival'] = $rival;
        }

        if ($estadio) {
            $conditions[] = 'fut_t_estadios.nombre_estadio = :estadio';
            $parameters['estadio'] = $estadio;
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Ejecutar la consulta
        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare($sql);
        $result = $statement->executeQuery($parameters);

        // Obtener los resultados
        $partidos = $result->fetchAllAssociative();

        // Retornar los resultados en formato JSON
        return $this->json($partidos);
    }
    
    /**
    * @Route("/api/add-productos", name="api_add_productos", methods={"POST"})
    */
    public function addProductos(Request $request): Response
    {
        $apiKey = $request->headers->get('Api-Key-getFutTPartidos');
        $validApiKey = 'a1b2c3d4e5f67890abcd1234567890abcdef1234567890abcdef1234567890';

        if ($apiKey !== $validApiKey) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['nombre']) || !isset($data['descripcion']) || !isset($data['precio']) || !isset($data['stock'])) {
            return new JsonResponse(['error' => 'Missing data'], Response::HTTP_BAD_REQUEST);
        }

        $producto = new \App\Entity\PEDIDOS\PedProductos();
        $producto->setNombre($data['nombre']);
        $producto->setDescripcion($data['descripcion']);
        $producto->setPrecio($data['precio']);
        $producto->setStock($data['stock']);

        $this->entityManager->persist($producto);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product created!'], Response::HTTP_CREATED);
    }
    
    /**
        * @Route("/api/datos-cronicas", name="api_datos_cronicas", methods={"POST"})
     */
    public function getDatosCronicas(Request $request): Response
    {
        
        $apiKey = $request->headers->get('Api-Key-getFutTPartidos');
        $validApiKey = 'a1b2c3d4e5f67890abcd1234567890abcdef1234567890abcdef1234567890';

        if ($apiKey !== $validApiKey) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        $id_equipo = $data['id_equipo'] ?? null;
        $texto_cronica = $data['texto_cronica'] ?? null;
        $mejores = $data['mejores'] ?? null;
        $goleadores = $data['goleadores'] ?? null;
        $rival = $data['rival'] ?? null;
        
        $data = json_decode($request->getContent(), true);
                
        
        if ($id_equipo) {
            $fechaHoy = new \DateTime();
            $fechaInicio = (clone $fechaHoy)->modify('monday this week')->format('Y-m-d');
            $fechaFin = (clone $fechaHoy)->modify('sunday this week')->format('Y-m-d');

            $sql = "
                SELECT
                  fut_u_equipos_rivales.id_partido,
                  fut_u_equipos_rivales.id_equipo,
                  fut_u_equipos_rivales.id_rival,
                  fut_t_equipos.equipo,
                  fut_t_rivales.rival,
                  fut_u_equipos_rivales.fecha
                FROM
                  fut_u_equipos_rivales
                  INNER JOIN fut_t_equipos ON fut_u_equipos_rivales.id_equipo = fut_t_equipos.id_equipo
                  INNER JOIN fut_t_rivales ON fut_t_rivales.id_rival = fut_u_equipos_rivales.id_rival
                WHERE
                  fut_u_equipos_rivales.id_equipo = :id_equipo
                  AND fut_u_equipos_rivales.fecha >= :fechaInicio
                  AND fut_u_equipos_rivales.fecha <= :fechaFin
            ";

            $parameters = [
            'id_equipo' => $id_equipo,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
            ];

            $connection = $this->entityManager->getConnection();
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery($parameters);

            $partidos = $result->fetchAllAssociative();

            if (count($partidos) === 1) {
                
                $id_partido = $partidos[0]['id_partido'];
                $this->actualizarCronica($id_partido, $texto_cronica, $mejores, $goleadores);
                $responseData = $this->prepareResponseData($data, 'datos grabados correctamente');

                return new JsonResponse($responseData, Response::HTTP_OK);
                
            } elseif (count($partidos) > 1) {
                
                $partidoMasParecido = $this->encontrarMasParecido($rival, $partidos);
                $id_partido = $partidoMasParecido['id_partido'];
                $this->actualizarCronica($id_partido, $texto_cronica, $mejores, $goleadores);
                $responseData = $this->prepareResponseData($data, 'datos grabados correctamente');

                return new JsonResponse($responseData, Response::HTTP_OK);
                
            } else {
                
                $responseData = $this->prepareResponseData($data, 'ATENCION: Cronica no encontrada');

                return new JsonResponse($responseData, Response::HTTP_OK);
                
            }
        }
    }
    
    private function actualizarCronica($id_partido, $texto_cronica, $mejores, $goleadores)
    {
        $cronica = $this->entityManager->getRepository(FutTCronicas::class)->findOneBy(['idPartido' => $id_partido]);

        if (!$cronica) {
            $equipoRival = $this->entityManager->getRepository(FutUEquiposRivales::class)->find($id_partido);
            $cronica = new FutTCronicas();
            $cronica->setIdPartido($equipoRival);
        }

        if (!empty($texto_cronica)) {
            $cronica->setTextoCronica($texto_cronica);
        }

        if (!empty($mejores)) {
            $cronica->setMvp($mejores[0] ?? null);
            $cronica->setMvpSemana($mejores[1] ?? null);
        }

        if (!empty($goleadores)) {
            for ($i = 0; $i < min(7, count($goleadores)); $i++) {
                $setterGoleador = 'setGoleador' . ($i + 1);
                $setterNumeroGoles = 'setNumeroGoles' . ($i + 1);
                $cronica->$setterGoleador($goleadores[$i]['nombre'] ?? null);
                $cronica->$setterNumeroGoles($goleadores[$i]['goles'] ?? null);
            }
        }

        $this->entityManager->persist($cronica);
        $this->entityManager->flush();
    }
    
    private function prepareResponseData($data, $operationMessage)
    {
        $responseData = [
            'id_equipo' => $data['id_equipo'] ?? null,
            'texto_cronica' => substr($data['texto_cronica'] ?? '', 0, 30),
            'mejores' => $data['mejores'] ?? null,
            'goleadores' => $data['goleadores'] ?? null,
            'rival' => $data['rival'] ?? null,
            'operacion' => $operationMessage
        ];

        return $responseData;
    }
    
    private function encontrarMasParecido($rival, $partidos)
    {
        $masParecido = null;
        $maxSimilitud = 0;
        $indiceMasParecido = null;

        foreach ($partidos as $indice => $item) {
            similar_text($rival, $item['rival'], $percent);

            if ($percent > $maxSimilitud) {
                $maxSimilitud = $percent;
                $masParecido = $item;
                $indiceMasParecido = $indice;
            }
        }

        return $partidos[$indiceMasParecido];
    }
}
