<?php

namespace App\Controller\FICHEROS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use GuzzleHttp\Client as GuzzleClient;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\colmenar\FutTEquipos;
use App\Entity\colmenar\FutTRivales;

class CsvADriveController extends AbstractController
{
    private $parameters;
    private $entityManager;
    private $session;

    public function __construct(ParameterBagInterface $parameters, EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->parameters = $parameters;
        $this->entityManager = $entityManager;
        $this->session = $requestStack->getSession();
    }
    
    #[Route('/ver-csv-a-drive', name: 'ver_csv_a_drive')]
    public function verCsvADrive(Request $request): Response
    {
        return $this->render('FICHEROS/csv_a_drive/portada_csv.html.twig', [
            'hola' => 'hola',
        ]);
    }
    
    #[Route('/csv-a-drive-partidos', name: 'csv_a_drive_partidos')]
    public function csvADrivePartidos(Request $request): Response
    {
        $accessToken = $this->getGoogleAccessToken();
        if (!$accessToken || !$this->checkDriveScope($accessToken)) {
            return $this->redirectToRoute('obtener_permisos_drive');
        }

        // Realizar la consulta SQL
        $connection = $this->entityManager->getConnection();
        $sql = "
            SELECT
              `fut_u_equipos_rivales`.`fecha`,
              `fut_u_equipos_rivales`.`horario`,
              `fut_t_equipos`.`equipo`,
              `fut_t_rivales`.`rival`,
              `fut_u_equipos_rivales`.`resultado_local`,
              `fut_u_equipos_rivales`.`resultado_visitante`,
              `fut_t_estadios`.`nombre_estadio`,
              `fut_t_detalle_tipo_partido`.`nombre_detalle`,
              `fut_t_tipo_partido`.`nombre_tipo_partido`,
              `fut_u_equipos_rivales`.`local`
            FROM
              `fut_u_equipos_rivales`
              INNER JOIN `fut_t_detalle_tipo_partido` ON
                `fut_t_detalle_tipo_partido`.`id_detalle_tipo_partido` =
                `fut_u_equipos_rivales`.`id_detalle_tipo_partido`
              INNER JOIN `fut_t_tipo_partido` ON `fut_t_tipo_partido`.`id_tipo_partido` =
                `fut_t_detalle_tipo_partido`.`id_tipo_partido`
              INNER JOIN `fut_t_equipos` ON `fut_t_equipos`.`id_equipo` =
                `fut_u_equipos_rivales`.`id_equipo`
              INNER JOIN `fut_t_rivales` ON `fut_t_rivales`.`id_rival` =
                `fut_u_equipos_rivales`.`id_rival`
              INNER JOIN `fut_t_estadios` ON `fut_t_estadios`.`id_estadio` =
                `fut_u_equipos_rivales`.`id_estadio`
        ";
        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery();
        $results = $resultSet->fetchAllAssociative();

        // Crear un nuevo objeto de hoja de cálculo
        $spreadsheet = new Spreadsheet();

        // Crear la hoja "Partidos"
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Partidos');

        // Crear el contenido de la hoja "Partidos"
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Horario');
        $sheet->setCellValue('C1', 'Equipo');
        $sheet->setCellValue('D1', 'Rival');
        $sheet->setCellValue('E1', 'Resultado Local');
        $sheet->setCellValue('F1', 'Resultado Visitante');
        $sheet->setCellValue('G1', 'Nombre Estadio');
        $sheet->setCellValue('H1', 'Nombre Detalle');
        $sheet->setCellValue('I1', 'Nombre Tipo Partido');
        $sheet->setCellValue('J1', 'Local');

        $row = 2;
        foreach ($results as $result) {
            $sheet->setCellValue('A' . $row, $result['fecha']);
            $sheet->setCellValue('B' . $row, $result['horario']);
            $sheet->setCellValue('C' . $row, $result['equipo']);
            $sheet->setCellValue('D' . $row, $result['rival']);
            $sheet->setCellValue('E' . $row, $result['resultado_local']);
            $sheet->setCellValue('F' . $row, $result['resultado_visitante']);
            $sheet->setCellValue('G' . $row, $result['nombre_estadio']);
            $sheet->setCellValue('H' . $row, $result['nombre_detalle']);
            $sheet->setCellValue('I' . $row, $result['nombre_tipo_partido']);
            $sheet->setCellValue('J' . $row, $result['local']);
            $row++;
        }

        // Guardar el archivo XLSX
        $xlsxFilename = $this->parameters->get('kernel.project_dir') . '/templates/FICHEROS/_CSV/partidos.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxFilename);

        // Subir el archivo a Google Drive
        try {
            $this->uploadCsvToGoogleDrive($xlsxFilename, $accessToken);
            $this->addFlash('notice', 'Partidos exportados correctamente');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al exportar partidos: ' . $e->getMessage());
        }

        return $this->render('FICHEROS/csv_a_drive/portada_csv.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/csv-a-drive-equipos', name: 'csv_a_drive_equipos')]
    public function csvADriveEquipos(Request $request): Response
    {
        $accessToken = $this->getGoogleAccessToken();
        if (!$accessToken || !$this->checkDriveScope($accessToken)) {
            return $this->redirectToRoute('obtener_permisos_drive');
        }

        // Crear un nuevo objeto de hoja de cálculo
        $spreadsheet = new Spreadsheet();

        // Crear la hoja "Equipos"
        $equiposSheet = $spreadsheet->getActiveSheet();
        $equiposSheet->setTitle('Equipos');

        $futTEquipos = $this->entityManager->getRepository(FutTEquipos::class)->findAll();

        // Crear el contenido de la hoja "Equipos"
        $equiposSheet->setCellValue('A1', 'ID');
        $equiposSheet->setCellValue('B1', 'IdRivalBis');
        $equiposSheet->setCellValue('C1', 'IdTipoEquipo');
        $equiposSheet->setCellValue('D1', 'Equipo');
        $equiposSheet->setCellValue('E1', 'EquipoMenu');
        $equiposSheet->setCellValue('F1', 'Imagen');
        $equiposSheet->setCellValue('G1', 'Orden');
        $equiposSheet->setCellValue('H1', 'Patrocinio');
        $equiposSheet->setCellValue('I1', 'WebPatrocinio');
        $equiposSheet->setCellValue('J1', 'LogoPatrocinio');
        $equiposSheet->setCellValue('K1', 'Clasificacion');
        $equiposSheet->setCellValue('L1', 'HistoricoClasificacion');
        $equiposSheet->setCellValue('M1', 'Calendario');
        $equiposSheet->setCellValue('N1', 'UltimaJornada');
        $equiposSheet->setCellValue('O1', 'Goleadores');
        $equiposSheet->setCellValue('P1', 'CompeticionActual');
        $equiposSheet->setCellValue('Q1', 'Sexo');
        $equiposSheet->setCellValue('R1', 'CodEquGesdep');

        $row = 2;
        foreach ($futTEquipos as $equipo) {
            $equiposSheet->setCellValue('A' . $row, $equipo->getIdEquipo());
            $equiposSheet->setCellValue('B' . $row, $equipo->getIdRivalBis());
            $equiposSheet->setCellValue('C' . $row, $equipo->getIdTipoEquipo());
            $equiposSheet->setCellValue('D' . $row, $this->sanitizeForCsv($equipo->getEquipo()));
            $equiposSheet->setCellValue('E' . $row, $this->sanitizeForCsv($equipo->getEquipoMenu()));
            $equiposSheet->setCellValue('F' . $row, $this->sanitizeForCsv($equipo->getImagen()));
            $equiposSheet->setCellValue('G' . $row, $equipo->getOrden());
            $equiposSheet->setCellValue('H' . $row, $this->sanitizeForCsv($equipo->getPatrocinio()));
            $equiposSheet->setCellValue('I' . $row, $this->sanitizeForCsv($equipo->getWebPatrocinio()));
            $equiposSheet->setCellValue('J' . $row, $this->sanitizeForCsv($equipo->getLogoPatrocinio()));
            $equiposSheet->setCellValue('K' . $row, $this->sanitizeForCsv($equipo->getClasificacion()));
            $equiposSheet->setCellValue('L' . $row, $this->sanitizeForCsv($equipo->getHistoricoClasificacion()));
            $equiposSheet->setCellValue('M' . $row, $this->sanitizeForCsv($equipo->getCalendario()));
            $equiposSheet->setCellValue('N' . $row, $this->sanitizeForCsv($equipo->getUltimaJornada()));
            $equiposSheet->setCellValue('O' . $row, $this->sanitizeForCsv($equipo->getGoleadores()));
            $equiposSheet->setCellValue('P' . $row, $this->sanitizeForCsv($equipo->getCompeticionActual()));
            $equiposSheet->setCellValue('Q' . $row, $this->sanitizeForCsv($equipo->getSexo()));
            $equiposSheet->setCellValue('R' . $row, $this->sanitizeForCsv($equipo->getCodEquGesdep()));
            $row++;
        }

        // Crear la hoja "Rivales"
        $rivalesSheet = $spreadsheet->createSheet();
        $rivalesSheet->setTitle('Rivales');

        $futTRivales = $this->entityManager->getRepository(FutTRivales::class)->findAll();

        // Crear el contenido de la hoja "Rivales"
        $rivalesSheet->setCellValue('A1', 'ID');
        $rivalesSheet->setCellValue('B1', 'Rival');
        $rivalesSheet->setCellValue('C1', 'Camiseta');
        $rivalesSheet->setCellValue('D1', 'Pantalon');
        $rivalesSheet->setCellValue('E1', 'Medias');
        $rivalesSheet->setCellValue('F1', 'ComprobadaEquipacion');
        $rivalesSheet->setCellValue('G1', 'CodEqFed');
        $rivalesSheet->setCellValue('H1', 'NombreEqFed');
        $rivalesSheet->setCellValue('I1', 'LocalidadFed');
        $rivalesSheet->setCellValue('J1', 'ProvinciaFed');

        $row = 2;
        foreach ($futTRivales as $rival) {
            $rivalesSheet->setCellValue('A' . $row, $rival->getIdRival());
            $rivalesSheet->setCellValue('B' . $row, $this->sanitizeForCsv($rival->getRival()));
            $rivalesSheet->setCellValue('C' . $row, $this->sanitizeForCsv($rival->getCamiseta()));
            $rivalesSheet->setCellValue('D' . $row, $this->sanitizeForCsv($rival->getPantalon()));
            $rivalesSheet->setCellValue('E' . $row, $this->sanitizeForCsv($rival->getMedias()));
            $rivalesSheet->setCellValue('F' . $row, $this->sanitizeForCsv($rival->getComprobadaEquipacion()));
            $rivalesSheet->setCellValue('G' . $row, $rival->getCodEqFed());
            $rivalesSheet->setCellValue('H' . $row, $this->sanitizeForCsv($rival->getNombreEqFed()));
            $rivalesSheet->setCellValue('I' . $row, $this->sanitizeForCsv($rival->getLocalidadFed()));
            $rivalesSheet->setCellValue('J' . $row, $this->sanitizeForCsv($rival->getProvinciaFed()));
            $row++;
        }

        // Guardar el archivo XLSX
        $xlsxFilename = $this->parameters->get('kernel.project_dir') . '/templates/FICHEROS/_CSV/equipos_y_rivales.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxFilename);

        // Subir el archivo a Google Drive
        try {
            $this->uploadCsvToGoogleDrive($xlsxFilename, $accessToken);
            $this->addFlash('notice', 'Equipos y rivales exportados correctamente');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al exportar equipos y rivales: ' . $e->getMessage());
        }

        return $this->render('FICHEROS/csv_a_drive/portada_csv.html.twig', [
            'futTEquipos' => $futTEquipos,
        ]);
    }

    #[Route('/obtener-permisos-drive', name: 'obtener_permisos_drive')]
    public function obtenerPermisosDrive(Request $request, UrlGeneratorInterface $urlGenerator)
    {
        try {
            $client = new Client();
            $client->setClientId($this->getParameter("clientIDGoogle"));
            $client->setClientSecret($this->getParameter("clientSecretGoogle"));
            $redirectUri = $urlGenerator->generate('guardar_permisos_drive', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $client->setRedirectUri($redirectUri);
            $client->addScope('openid');
            $client->addScope('email');
            $client->addScope('profile');
            $client->addScope('https://www.googleapis.com/auth/drive.file'); // Añadir el alcance para Google Drive

            $authUrl = $client->createAuthUrl();
            return new RedirectResponse($authUrl);
        } catch (\Exception $e) {
            $this->addFlash('error', 'No se pudo obtener la URL de autenticación de Google. Por favor, inténtalo de nuevo más tarde.');
            return $this->redirectToRoute('login'); // O redirige a una página de error.
        }
    }

    #[Route('/guardar-permisos-drive', name: 'guardar_permisos_drive')]
    public function guardarPermisosDrive(Request $request, UrlGeneratorInterface $urlGenerator)
    {
        $googleCode = $request->query->get('code');
        if (!$googleCode) {
            return $this->redirectToRoute('login');
        }

        $client = new Client();
        $client->setClientId($this->getParameter("clientIDGoogle"));
        $client->setClientSecret($this->getParameter("clientSecretGoogle"));
        $redirectUri = $urlGenerator->generate('guardar_permisos_drive', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $client->setRedirectUri($redirectUri);
        $client->addScope('openid');
        $client->addScope('email');
        $client->addScope('profile');
        $client->addScope('https://www.googleapis.com/auth/drive.file'); // Añadir el alcance para Google Drive

        try {
            // Obtén el token de acceso desde la respuesta de Google
            $googleAccessToken = $client->fetchAccessTokenWithAuthCode($googleCode);
            $session = $request->getSession();
            $session->set('google_access_token', $googleAccessToken['access_token']);
            if (isset($googleAccessToken['id_token'])) {
                $session->set('google_id_token', $googleAccessToken['id_token']);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('login');
        }

        return $this->redirectToRoute('ver_csv_a_drive');
    }

    private function uploadCsvToGoogleDrive(string $csvFilename, string $accessToken)
    {
        $client = new \Google\Client();
        $client->setAccessToken($accessToken);

        $service = new \Google\Service\Drive($client);

        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name' => basename($csvFilename),
            // 'parents' => ['15-dZVTUD-D6egcc3mj58NlWALT9MwZGb']  // Esto solo se necesita al crear el archivo
        ]);

        $content = file_get_contents($csvFilename);

        // Buscar el archivo existente en Google Drive
        $existingFileId = $this->getExistingFileId($service, basename($csvFilename));

        if ($existingFileId) {
            // Si el archivo existe, actualizarlo
            $file = $service->files->update($existingFileId, $fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
        } else {
            // Si el archivo no existe, crearlo
            $fileMetadata->setParents(['15-dZVTUD-D6egcc3mj58NlWALT9MwZGb']); // Asignar la carpeta al crear el archivo
            $file = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
        }
    }

    private function getExistingFileId($service, $fileName)
    {
        $response = $service->files->listFiles([
            'q' => "name = '$fileName' and trashed = false",
            'spaces' => 'drive',
            'fields' => 'files(id, name)',
            'pageSize' => 1
        ]);

        if (count($response->files) > 0) {
            return $response->files[0]->id;
        }

        return null;
    }

    private function getGoogleAccessToken()
    {
        return $this->session->get('google_access_token');
    }

    private function checkDriveScope($accessToken)
    {
        $guzzleClient = new GuzzleClient();
        $response = $guzzleClient->request('GET', 'https://www.googleapis.com/oauth2/v1/tokeninfo', [
            'query' => ['access_token' => $accessToken]
        ]);

        if ($response->getStatusCode() == 200) {
            $tokenInfo = json_decode($response->getBody(), true);
            if (isset($tokenInfo['scope']) && in_array('https://www.googleapis.com/auth/drive.file', explode(' ', $tokenInfo['scope']))) {
                return true;
            }
        }

        return false;
    }

    private function sanitizeForCsv($value)
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }
}
