<?php

namespace App\Controller\FICHEROS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\intranet\IntFicheros;
use App\Repository\FICHEROS\FicherosRepository;
use App\Services\LeerFicheros;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class CargaFicherosController extends AbstractController
{
    private $parameters;
    private $leerFicheros;
    private $entityManager;
    
    public function __construct(ParameterBagInterface $parameters, LeerFicheros $leerFicheros, EntityManagerInterface $entityManager)
    {
        $this->parameters = $parameters;
        $this->leerFicheros = $leerFicheros;
        $this->entityManager = $entityManager;
    }
    
    #[Route('/ficheros/ver-ficheros', name: 'ver_ficheros')]
    public function verFicheros(Request $request): Response
    {
        $ficheros = $this->entityManager->getRepository(IntFicheros::class)->findAll();

        return $this->render('FICHEROS/carga_ficheros/ver_ficheros.html.twig', [
            'ficheros' => $ficheros,
        ]);
    }
    
    #[Route('/ficheros/carga-ficheros', name: 'cargar_ficheros')]
    public function cargarFicheros(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $files = $request->files->get('archivos'); // Cambio del nombre del input en la plantilla

            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->guessExtension();
                    $size = $file->getSize();
                    $rutaFisica = $this->parameters->get('ruta_ficheros_fisicos').'/'. $extension . '/' . $originalFilename . '.' . $extension;
                    $nombreFichero = $originalFilename.'.'.$extension;
                    if (!$this->comprobarTamano($size,$nombreFichero)) continue;
                    if (!$this->comprobarExtension($extension,$nombreFichero)) continue;
                    
                    $fichero = new IntFicheros();
                    $fichero->setNombre($originalFilename);
                    $fichero->setExtension($extension);
                    $fichero->setTamaño($size);
                    $fichero->setRuta($rutaFisica);
                    
                    $this->entityManager->persist($fichero);
                    $this->entityManager->flush();
                    
                    // Mover el fichero físico a la ubicación adecuada
                    if (!is_dir(dirname($rutaFisica))) {
                        mkdir(dirname($rutaFisica), 0755, true);
                    }
                    $file->move(dirname($rutaFisica), basename($rutaFisica));
                    $this->addFlash('notice', 'Fichero: '.$originalFilename.$extension.' subido correctamente.');
                }
            }
        }

        return $this->redirectToRoute('ver_ficheros');
    }
    
    #[Route('/ficheros/descargar/{id}', name: 'descargar_fichero')]
    public function descargarFichero(IntFicheros $fichero): Response
    {
        $rutaFisica = $fichero->getRuta();

        if (file_exists($rutaFisica)) {
            $response = new BinaryFileResponse($rutaFisica);
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fichero->getNombre() . '.' . $fichero->getExtension());

            return $response;
        }

        throw $this->createNotFoundException('El fichero no existe.');
    }
    
    #[Route('/ficheros/eliminar/{id}', name: 'eliminar_fichero')]
    public function eliminarFichero(Request $request, $id): Response
    {
        $fichero = $this->entityManager->getRepository(IntFicheros::class)->find($id);

        if (!$fichero) {
            throw $this->createNotFoundException('No se encontró el fichero con el ID: '.$id);
        }

        // Eliminar el fichero físico
        $filesystem = new Filesystem();
        $rutaFichero = $this->parameters->get('ruta_ficheros_fisicos').'/'.$fichero->getExtension().'/'.$fichero->getNombre().'.'.$fichero->getExtension();

        if ($filesystem->exists($rutaFichero)) {
            $filesystem->remove($rutaFichero);
        }

        // Eliminar el registro de la base de datos
        $this->entityManager->remove($fichero);
        $this->entityManager->flush();

        $this->addFlash('warning', 'Fichero eliminado correctamente');
        return $this->redirectToRoute('ver_ficheros');
    }
    
    #[Route('/ficheros/leer-fichero/{id}', name: 'leer_fichero')]
    public function leerFichero(Request $request, FicherosRepository $ficherosRepository, $id): Response
    {
        // Obtener el fichero por su ID desde el repositorio
        $fichero = $this->entityManager->getRepository(IntFicheros::class)->find($id);

        if (!$fichero) {
            $this->addFlash('error', 'No existe el fichero en la BD con el ID: '.$id);
            return $this->redirectToRoute('ver_ficheros');
        }

        // Utilizar el servicio Ficheros para convertir el fichero a texto
        $textoFichero = $this->leerFicheros->convertToText($fichero,$this->parameters->get('arrayExtensionesLeer'));
        return new JsonResponse(['texto' => $textoFichero]);
        $this->addFlash('error', $textoFichero);
        return $this->redirectToRoute('ver_ficheros'); // Cambia el nombre de la ruta si es necesario
    }
    
    private function comprobarExtension($ext,$nombreFichero)
    {
        if (in_array($ext, $this->parameters->get('arrayExtensionesCargar'))) {
            if($ext == 'doc' or $ext == 'rtf'){
                $this->addFlash('error', 'La extension del fichero '.$nombreFichero.' no está soportada. Cambiar el fichero a .docx y vuelve a cargarlo en el sistema');
                return false;
            } else {
                return true;
            }
        } else {
            $this->addFlash('error', 'La extension del fichero '.$nombreFichero.' no está soportada por el sistema.');
            return false;
        } 
    }
    
    private function comprobarTamano($size,$nombreFichero)
    {
        // Tamaño máximo permitido en bytes (50 megabytes)
        $tamanoMaximo = 25 * 1024 * 1024; // 25 MB en bytes

        // Comprueba si el tamaño es mayor al máximo permitido
        if ($size > $tamanoMaximo) {
            $this->addFlash('error', 'El Tamaño del fichero: '.$nombreFichero.' es superior a 50 mb. y no puede ser subido al sistema.');
            return false;
        }

        return true;
    }
    
}