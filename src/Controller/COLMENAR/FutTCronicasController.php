<?php

namespace App\Controller\COLMENAR;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Services\OpenAIService;
use Symfony\Component\Filesystem\Filesystem;

use App\Entity\colmenar\FutTCronicas;
use App\Form\FutTCronicasType;
use App\Entity\colmenar\FutUEquiposRivales;
use App\Entity\colmenar\FutTJugadoresNew;
use App\Entity\colmenar\FutTEquipos;
use App\Entity\colmenar\FutTImagenesCronicas;
use App\Repository\COLMENAR\FutTCronicasRepository;
use App\Repository\COLMENAR\FutTImagenesCronicasRepository;

class FutTCronicasController extends AbstractController
{
    private $entityManager;
    private $client;
    private $sid;
    private $token;
    private $twilio;
    private $smsEnvio;
    private $smsTwilio;
    private $whatsappEnvio;
    private $whatsappTwilio;
    private $security;
    
    public function __construct(ManagerRegistry $doctrine, HttpClientInterface $client, ParameterBagInterface $parameterBag, Security $security)
    {
        $this->entityManager = $doctrine->getManager('colmenar');
        $this->client = $client;
                
        $this->sid  = $parameterBag->get('sid_twilio');        
        $this->token = $parameterBag->get('token_twilio');

        $this->smsEnvio = $parameterBag->get('sms_envio');
        $this->smsTwilio = $parameterBag->get('sms_twilio');
        
        $this->whatsappEnvio = $parameterBag->get('whatsapp_envio');
        $this->whatsappTwilio = $parameterBag->get('whatsapp_twilio');
        
        $this->security = $security;
    }
    
    #[Route('/colmenar/cronicas', name: 'app_fut_t_cronicas_index')]
    public function index(): Response
    {
        return $this->render('web_colmenar/fut_t_cronicas/index.html.twig', [
            'hola' => null,
        ]);
    }
    
    #[Route('/colmenar/cronicas/mpv', name: 'app_fut_t_cronicas_mvp')]
    #[Route('/colmenar/cronicas/mpv/{week}', name: 'app_fut_t_cronicas_mvp_week')]
    public function mvp(Request $request, $week = null): Response
    {
        $currentDate = new \DateTime();
        $weekSelected = $week;

        if ($weekSelected) {
            [$startDate, $endDate] = explode(' - ', $weekSelected);
            $fechaInicio = \DateTime::createFromFormat('d-m-Y', trim($startDate));
            $fechaFin = \DateTime::createFromFormat('d-m-Y', trim($endDate));

            if (!$fechaInicio || !$fechaFin) {
                // If date parsing fails, fall back to the current week
                $fechaInicio = (clone $currentDate)->modify('monday this week');
                $fechaFin = (clone $currentDate)->modify('sunday this week');
                $weekSelected = $fechaInicio->format('d-m-Y') . ' - ' . $fechaFin->format('d-m-Y');
            }
        } else {
            // Calculate default date range (current week from Monday to Sunday)
            $fechaInicio = (clone $currentDate)->modify('monday this week');
            $fechaFin = (clone $currentDate)->modify('sunday this week');
            $weekSelected = $fechaInicio->format('d-m-Y') . ' - ' . $fechaFin->format('d-m-Y');
        }

        $weeksWithMatches = $this->entityManager
            ->getRepository(FutUEquiposRivales::class)
            ->findWeeksWithMatches();

        $partidos = $this->entityManager
            ->getRepository(FutUEquiposRivales::class)
            ->findPartidosPorFechasMVP($fechaInicio, $fechaFin);

        return $this->render('web_colmenar/fut_t_cronicas/mvp.html.twig', [
            'partidos' => $partidos,
            'weeks' => $weeksWithMatches,
            'selectedWeek' => $weekSelected,
            'title' => 'MVPs de la semana (' . $fechaInicio->format('d-m-Y') . ' - ' . $fechaFin->format('d-m-Y') . ')',
        ]);
    }

    
    #[Route('/colmenar/cronicas/fut-t-cronicas-semana-actual', name: 'app_fut_t_cronicas_semana_actual')]
    #[Route('/colmenar/cronicas/fut-t-cronicas-mes-actual', name: 'app_fut_t_cronicas_mes_actual')]
    #[Route('/colmenar/cronicas/fut-t-cronicas-temporada-actual', name: 'app_fut_t_cronicas_temporada_actual')]
    public function cronicas(Request $request): Response
    {
        $routeName = $request->attributes->get('_route');
        $filtro = 'semana';

        if ($routeName === 'app_fut_t_cronicas_mes_actual') {
            $filtro = 'mes';
        } elseif ($routeName === 'app_fut_t_cronicas_temporada_actual') {
            $filtro = 'temporada';
        }

        $partidos = $this->entityManager
                     ->getRepository(FutUEquiposRivales::class)
                     ->findPartidosPorFechas($filtro);

        $titulo = $this->entityManager
                       ->getRepository(FutUEquiposRivales::class)
                       ->generarTitulo($filtro);

        return $this->render('web_colmenar/fut_t_cronicas/para_mi.html.twig', [
            'partidos' => $partidos,
            'filtro' => $filtro,
            'titulo' => $titulo,
        ]);
    }

    #[Route('/colmenar/cronicas/cargar-jugadores', name: 'app_fut_t_cargar_jugadores')]
    public function cargarJugadores(): Response
    {
        $jugadoresRepo = $this->entityManager->getRepository(FutTJugadoresNew::class);
        $jugadores = $jugadoresRepo->findAll();

        return $this->render('web_colmenar/fut_t_cronicas/jugadores.html.twig', [
            'jugadores' => $jugadores,
        ]);
    }
    
    #[Route('/colmenar/cargar-jugadores-csv', name: 'cargar_jugadores_csv')]
    public function cargarJugadoresCsv(Request $request): Response
    {
        $archivosCSV = $request->files->get('csv_file');

        if ($archivosCSV) {
            foreach ($archivosCSV as $archivoCSV) {
                $rutaArchivo = $archivoCSV->getRealPath();
                if (($handle = fopen($rutaArchivo, 'r')) !== FALSE) {
                    stream_filter_append($handle, 'convert.iconv.UTF-16LE/UTF-8');
                    fgetcsv($handle, 1000, ";");

                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        $nombreCompleto = explode(',', $data[2]);
                        if (count($nombreCompleto) >= 2) {
                            $apellidos = trim($nombreCompleto[0]);
                            $nombre = trim($nombreCompleto[1]);
                        } else {
                            $apellidos = '';
                            $nombre = trim($nombreCompleto[0]);
                        }

                        $codJugGesdep = str_replace('Clave: ', '', $data[3]);

                        $jugador = $this->entityManager->getRepository(FutTJugadoresNew::class)->findOneBy(['codJugGesdep' => $codJugGesdep]);

                        if (!$jugador) {
                            $jugador = new FutTJugadoresNew();
                            $jugador->setCodJugGesdep($codJugGesdep);
                        }

                        $jugador->setNombre($nombre);
                        $jugador->setApellidos($apellidos);
                        $jugador->setCorreoJugador($data[4]);
                        $jugador->setCorreoPadre($data[5]);
                        $jugador->setCorreoMadre($data[6]);

                        $equipoAbreviado = $data[1];
                        $equipo = $this->entityManager->getRepository(FutTEquipos::class)->findOneBy(['codEquGesdep' => $equipoAbreviado]);
                        if ($equipo) {
                            $jugador->setIdEquipo($equipo);
                        }

                        $this->entityManager->persist($jugador);
                    }
                    fclose($handle);
                }
            }
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_fut_t_cargar_jugadores');
    }
    
    #[Route('/colmenar/cronicas/entrenadores/cronicas-semana-entrenadores', name: 'app_fut_t_cronicas_semana')]
    public function cronicasSemana(): Response
    {
        $connection = $this->entityManager->getConnection();
    
        $fechaHoy = new \DateTime();

        $diaDeLaSemana = (int)$fechaHoy->format('N');

        if ($diaDeLaSemana === 1) {
            $fechaInicio = (clone $fechaHoy)->modify('previous monday');
            $fechaFin = (clone $fechaHoy)->modify('previous sunday');
        } else {
            $fechaInicio = (clone $fechaHoy)->modify('monday this week');
            $fechaFin = (clone $fechaHoy)->modify('sunday this week');
        }

        $fechaInicio = $fechaInicio->format('Y-m-d');
        $fechaFin = $fechaFin->format('Y-m-d');

        $sql = "SELECT fut_u_equipos_rivales.fecha, fut_t_equipos.id_tipo_equipo, fut_t_equipos.equipo, fut_t_equipos.equipo_menu, fut_t_rivales.rival, fut_u_equipos_rivales.resultado_local, fut_u_equipos_rivales.resultado_visitante, fut_u_equipos_rivales.horario, fut_u_equipos_rivales.local, fut_u_equipos_rivales.id_equipo, fut_u_equipos_rivales.id_estadio, fut_t_estadios.nombre_estadio,fut_t_equipos.orden,fut_u_equipos_rivales.id_detalle_tipo_partido, fut_t_tipo_partido.id_tipo_partido, fut_t_tipo_partido.nombre_tipo_partido,fut_t_detalle_tipo_partido.nombre_detalle,fut_u_equipos_rivales.id_partido,fut_u_equipos_rivales.observaciones, fut_u_equipos_rivales.autocar
                FROM fut_t_equipos 
                INNER JOIN fut_u_equipos_rivales ON fut_u_equipos_rivales.id_equipo = fut_t_equipos.id_equipo 
                INNER JOIN fut_t_rivales ON fut_u_equipos_rivales.id_rival = fut_t_rivales.id_rival 
                LEFT JOIN fut_t_estadios ON fut_t_estadios.id_estadio = fut_u_equipos_rivales.id_estadio 
                LEFT JOIN fut_t_detalle_tipo_partido ON fut_t_detalle_tipo_partido.id_detalle_tipo_partido = fut_u_equipos_rivales.id_detalle_tipo_partido 
                LEFT JOIN fut_t_tipo_partido ON fut_t_detalle_tipo_partido.id_tipo_partido = fut_t_tipo_partido.id_tipo_partido 
                WHERE fut_u_equipos_rivales.fecha >= :fechaInicio AND fut_u_equipos_rivales.fecha <= :fechaFin AND fut_u_equipos_rivales.mostrar IN (0,1) ORDER BY fut_t_equipos.orden ASC, fut_u_equipos_rivales.fecha DESC, fut_u_equipos_rivales.horario ASC";

        $partidos = $connection->executeQuery($sql, [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ])->fetchAllAssociative();

        return $this->render('web_colmenar/fut_t_cronicas/cronicas_semana.html.twig', [
            'partidos' => $partidos,
        ]);
    }
    
    #[Route('/colmenar/cronicas/entrenadores/cronicas-entrenadores/add/{id_partido}', name: 'app_fut_t_cronicas_add')]
    public function addOrEditCronica(Request $request, OpenAIService $openAIService, $id_partido): Response
    {
        $cronica = $this->entityManager->getRepository(FutTCronicas::class)->findOneBy(['idPartido' => $id_partido]);

        if (!$cronica) {
            $cronica = new FutTCronicas();
            $partido = $this->entityManager->getRepository(FutUEquiposRivales::class)->find($id_partido);
            if (!$partido) {
                throw $this->createNotFoundException('No se encontró el partido con el id proporcionado.');
            }
            $cronica->setIdPartido($partido);
            $this->entityManager->persist($cronica);
            $this->entityManager->flush();
        }

        $partido = $this->entityManager->getRepository(FutUEquiposRivales::class)->find($id_partido);
        if (!$partido) {
            throw $this->createNotFoundException('No se encontró el partido con el id proporcionado.');
        }
        $id_equipo = $partido->getIdEquipo();

        $form = $this->createForm(FutTCronicasType::class, $cronica, [
            'id_equipo' => $id_equipo
        ]);
        $form->get('resultadoLocal')->setData($partido->getResultadoLocal());
        $form->get('resultadoVisitante')->setData($partido->getResultadoVisitante());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$cronica->getIdPartido()) {
                $partido = $this->entityManager->getRepository(FutUEquiposRivales::class)->find($id_partido);
                if ($partido) {
                    $cronica->setIdPartido($partido);
                }
            }
            $partido->setResultadoLocal($form->get('resultadoLocal')->getData());
            $partido->setResultadoVisitante($form->get('resultadoVisitante')->getData());

            //PARTE DE CHATGPT
            if ($form->get('usarChatGPT')->getData() === 'si') {
                $originalText = $cronica->getTextoCronica();
                $expandedText = $openAIService->generateCronicaPartido($cronica,$partido);

                if ($expandedText) {
                    $cronica->setTextoCronicaChatgpt($expandedText);
                }
            }

            $cronica->setPublicadoEnRedes($form->get('publicadoEnRedes')->getData());
            $cronica->setEnviadoWhatsapp($form->get('enviadoWhatsapp')->getData());

            $this->entityManager->persist($cronica);

            // Manejar archivos subidos
            $imagenes = $form->get('imagenes')->getData();
            $this->manejarImagenesCronica($cronica, $imagenes);

            //GUARDAMOS EL RESULTADO DEL PARTIDO
            $resultadoLocal = $form->get('resultadoLocal')->getData();
            $resultadoVisitante = $form->get('resultadoVisitante')->getData();

            $partido = $cronica->getIdPartido();

            if ($resultadoLocal !== null && $resultadoVisitante !== null) {
                $partido->setResultadoLocal($resultadoLocal);
                $partido->setResultadoVisitante($resultadoVisitante);

                $this->entityManager->persist($partido);
                $this->entityManager->flush();
            }

            //PARTE DE WHATSAPP
            $textoMensaje = $this->construirTextoMensaje($partido, $cronica);
            $this->dividirYEnviarTextoWhatsApp($textoMensaje);

            $imagenesCronica = $this->entityManager->getRepository(FutTImagenesCronicas::class)->findBy(['idCronica' => $cronica->getIdCronica()]);
            $this->enviarImagenesWhatsApp($imagenesCronica);

            $this->addFlash('notice', 'Cronica agregada correctamente');
            if ($this->security->isGranted('ROLE_NACHO')) {
                return $this->redirectToRoute('app_fut_t_cronicas_semana_actual');
            }

            return $this->redirectToRoute('app_fut_t_cronicas_index');
        }

        $imagenesCronica = $this->entityManager->getRepository(FutTImagenesCronicas::class)->findBy(['idCronica' => $cronica->getIdCronica()]);

        return $this->render('web_colmenar/fut_t_cronicas/form.html.twig', [
            'cronica' => $cronica,
            'form' => $form->createView(),
            'imagenesCronica' => $imagenesCronica,
        ]);
    }
    
    #[Route('/colmenar/cronicas/fut-t-fotos-de-cronicas', name: 'app_fut_t_fotos_de_cronicas')]
    public function fotosDeCronicas(FutTCronicasRepository $cronicasRepo, FutTImagenesCronicasRepository $imagenesRepo): Response
    {
        $connection = $this->entityManager->getConnection();
        $sql = "
            SELECT
              fut_t_cronicas.id_partido,
              fut_t_cronicas.id_cronica,
              fut_t_imagenes_cronicas.id_imagen,
              fut_t_imagenes_cronicas.ruta,
              fut_u_equipos_rivales.fecha,
              fut_u_equipos_rivales.horario,
              fut_u_equipos_rivales.local,
              fut_t_equipos.equipo,
              fut_t_rivales.rival
            FROM
              fut_t_cronicas
              INNER JOIN fut_t_imagenes_cronicas ON fut_t_imagenes_cronicas.id_cronica = fut_t_cronicas.id_cronica
              INNER JOIN fut_u_equipos_rivales ON fut_t_cronicas.id_partido = fut_u_equipos_rivales.id_partido
              INNER JOIN fut_t_equipos ON fut_u_equipos_rivales.id_equipo = fut_t_equipos.id_equipo
              INNER JOIN fut_t_rivales ON fut_u_equipos_rivales.id_rival = fut_t_rivales.id_rival
        ";

        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $cronicasConImagenes = $resultSet->fetchAllAssociative();

        return $this->render('web_colmenar/fut_t_cronicas/fotos_cronicas.html.twig', [
            'cronicasConImagenes' => $cronicasConImagenes,
            'imagenes_cronicas_url_base' => $this->getParameter('imagenes_cronicas_url_base'),
        ]);
    }
    
    #[Route('/colmenar/cronicas/eliminar', name: 'app_fut_t_cronicas_eliminar_imagenes', methods: ['POST'])]
    public function eliminarImagenes(Request $request): Response
    {
        $imagenesIds = $request->request->all('eliminar');
        $imagenesDir = $this->getParameter('imagenes_cronicas_directory');

        if (!empty($imagenesIds)) {
            $repo = $this->entityManager->getRepository(FutTImagenesCronicas::class);
            $filesystem = new Filesystem();
            $deletedImages = [];
            $notFoundImages = [];

            foreach ($imagenesIds as $id) {
                $imagen = $repo->find($id);
                if ($imagen) {
                    $filePath = $imagenesDir . '/' . $imagen->getRuta();
                    if ($filesystem->exists($filePath)) {
                        $filesystem->remove($filePath);
                        $deletedImages[] = $filePath;
                    } else {
                        $notFoundImages[] = $filePath;
                    }
                    $this->entityManager->remove($imagen);
                } else {
                    $this->addFlash('warning', "Imagen no encontrada en la base de datos: ID " . $id);
                }
            }
            $this->entityManager->flush();

            if ($deletedImages) {
                $this->addFlash('success', "Se han eliminado correctamente las imágenes seleccionadas.");
            }
            if ($notFoundImages) {
                $this->addFlash('warning', "Algunas imágenes no se encontraron en el sistema de archivos.");
            }
        } else {
            $this->addFlash('warning', "No se seleccionaron imágenes para eliminar.");
        }

        return $this->redirectToRoute('app_fut_t_fotos_de_cronicas');
    }
    
    private function manejarImagenesCronica($cronica, $imagenes)
    {
        $extensionesPermitidas = ['png', 'jpg', 'jpeg', 'heic', 'heif', ];
        $maximoTamano = 5 * 1024 * 1024; // 5 MB expresados en bytes.

        foreach ($imagenes as $imagen) {
            if ($imagen) {
                $extensionArchivo = $imagen->guessExtension();
                $tamanoArchivo = $imagen->getSize();
                $nombreArchivo = $imagen->getClientOriginalName(); 

                // Comprobar la extensión
                if (!in_array(strtolower($extensionArchivo), $extensionesPermitidas)) {
                    $this->addFlash('error', "El archivo ({$nombreArchivo}) no ha sido grabado por no estar permitido su formato.");
                    continue; // Saltar al siguiente archivo sin procesar este.
                }

                // Comprobar el tamaño
                if ($tamanoArchivo > $maximoTamano) {
                    $this->addFlash('error', "El archivo ({$nombreArchivo}) excede el tamaño máximo permitido de 5MB.");
                    continue; // Saltar al siguiente archivo sin procesar este.
                }

                $nombreOriginal = pathinfo($nombreArchivo, PATHINFO_FILENAME);
                $nuevoNombreArchivo = $cronica->getIdCronica() . '_' . date('Ymd_His') . '.' . $extensionArchivo;

                try {
                    $imagen->move(
                        $this->getParameter('imagenes_cronicas_directory'),
                        $nuevoNombreArchivo
                    );
                } catch (FileException $e) {
                    // Manejo de excepciones al mover el archivo.
                    $this->addFlash('error', "Hubo un problema al subir el archivo ({$nombreArchivo}).");
                }

                $imagenCronica = new FutTImagenesCronicas();
                $imagenCronica->setRuta($nuevoNombreArchivo);
                $imagenCronica->setIdCronica($cronica);
                $this->entityManager->persist($imagenCronica);
            }
        }

        $this->entityManager->flush();
    }
    
    #[Route('/colmenar/cronicas/entrenadores/cronicas-entrenadores/eliminar-imagen/{id}', name: 'app_fut_t_cronicas_delete_imagen', methods: ['POST'])]
    public function eliminarImagen(int $id): Response
    {
        $imagen = $this->entityManager->getRepository(FutTImagenesCronicas::class)->find($id);

        if ($imagen) {
            $this->entityManager->remove($imagen);
            $this->entityManager->flush();

            $rutaImagen = $this->getParameter('imagenes_cronicas_directory') . '/' . $imagen->getRuta();
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }

            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_NOT_FOUND);
    }
    
    private function construirTextoMensaje($partido, $cronica)
    {
        $nombreEquipo = $partido->getIdEquipo()->getEquipo();
        $nombreRival = $partido->getIdRival()->getRival();
        $competicion = $partido->getCompeticion();
        $grupo = $partido->getGrupo();
        $jornada = $partido->getJornada();
        $resultadoLocal = $partido->getResultadoLocal();
        $resultadoVisitante = $partido->getResultadoVisitante();
        $local = $partido->getLocal() == 'si';

        $resultadoPartido = $local
        ? "$resultadoLocal-$resultadoVisitante $nombreEquipo"
        : "$resultadoVisitante-$resultadoLocal $nombreRival";


        $goles = '';
        for ($i = 1; $i <= 7; $i++) {
            $goleadorMethod = 'getGoleador' . $i;
            $numeroGolesMethod = 'getNumeroGoles' . $i;
            if (method_exists($cronica, $goleadorMethod) && $cronica->$goleadorMethod()) {
                $goleador = $cronica->$goleadorMethod();
                $numeroGoles = $cronica->$numeroGolesMethod();
                if ($numeroGoles) {
                    $goles .= $goleador . ' (' . $numeroGoles . '), ';
                }
            }
        }
        $goles = rtrim($goles, ', ');

        // MVP del Partido y de la Semana
        $mvpPartido = $cronica->getMvp() ? $cronica->getMvp() : 'No especificado';
        $mvpSemana = $cronica->getMvpSemana() ? $cronica->getMvpSemana() : 'No especificado';

        // Personaliza el cuerpo del mensaje con los datos recopilados
        $textoMensaje = "*" . strtoupper($nombreEquipo) . "*\n" // Nombre del equipo en mayúsculas y negrita
             . ($local ? "$nombreEquipo $resultadoLocal-$resultadoVisitante $nombreRival" : "$nombreRival $resultadoVisitante-$resultadoLocal $nombreEquipo") . "\n"
             . "$competicion $grupo Jornada $jornada\n\n" // CompeticiÃ³n, grupo y jornada
             . "*Crónica del partido*\n" // Titulo de la crónica
             . $cronica->getTextoCronica() . "\n\n" // Texto de la crónica
             . "*Goles:* $goles\n" // Detalle de goles
             . "*MVP Partido:* $mvpPartido\n" // MVP del partido
             . "*MVP Semana:* $mvpSemana"; // MVP de la semana

        return $textoMensaje;
    }
    
    private function enviarMensajeWhatsAppTexto($textoMensaje)
    {
        /*
         * UNIRSE A TWILIO: join guard-individual
         */
        $twilio = new Client($this->sid, $this->token);

        try {
            $message = $twilio->messages->create(
                "whatsapp:{$this->whatsappEnvio}",
                [
                    "from" => "whatsapp:{$this->whatsappTwilio}",
                    "body" => $textoMensaje,
                ]
            );

            return $message->sid;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    private function enviarImagenesWhatsApp($imagenesCronica) {
        foreach ($imagenesCronica as $imagenCronica) {
            $urlImagen = $this->getUrlImagen($imagenCronica);
            if ($urlImagen) {
                $this->enviarMensajeWhatsAppImagen($urlImagen);
            }
        }
    }

    private function enviarMensajeWhatsAppImagen($urlImagen) {
        $twilio = new Client($this->sid, $this->token);
        try {
            $message = $twilio->messages->create(
                "whatsapp:{$this->whatsappEnvio}",
                [
                    "from" => "whatsapp:{$this->whatsappTwilio}",
                    "mediaUrl" => [$urlImagen],
                ]
            );
        } catch (\Exception $e) {
            // Manejo de errores
        }
    }
         
    private function enviarWhatsApp($textoMensaje) {
        $twilio = new Client($this->sid, $this->token);
        try {
            $message = $twilio->messages->create(
                "whatsapp:{$this->whatsappEnvio}",
                [
                    "from" => "whatsapp:{$this->whatsappTwilio}",
                    "body" => $textoMensaje,
                ]
            );
        } catch (\Exception $e) {
            // Manejo de errores
        }
    }
    
    private function dividirYEnviarTextoWhatsApp($textoMensaje) {
        $maximoCaracteres = 1500;
        if (strlen($textoMensaje) > $maximoCaracteres) {
            // Divide el mensaje en partes
            $partesTexto = str_split($textoMensaje, $maximoCaracteres);
            foreach ($partesTexto as $indice => $parteTexto) {
                // Agrega la nota de continuación solo si no es la primera parte
                if ($indice > 0) {
                    $parteTexto = "(Continuación)\n" . $parteTexto;
                }
                $this->enviarMensajeWhatsAppTexto($parteTexto);
            }
        } else {
            // Si el mensaje no supera el máximo, lo envía directamente
            $this->enviarMensajeWhatsAppTexto($textoMensaje);
        }
    }
    
    private function getUrlImagen($imagenCronica) {
        $entorno = $this->getParameter('kernel.environment');
        $urlBase = $entorno === 'dev' 
                   ? $this->getParameter('url_aplicacion_local') 
                   : $this->getParameter('url_aplicacion');
        
        // Asegurarse de que la URL base termina con una barra
        if (!str_ends_with($urlBase, '/')) {
            $urlBase .= '/';
        }

    
        $rutaImagen = $this->getParameter('imagenes_cronicas_url_base') . $imagenCronica->getRuta();
        return $urlBase . $rutaImagen;
    }
    
}
