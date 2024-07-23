<?php

namespace App\Repository\COLMENAR;

use App\Entity\colmenar\IntPartidosLigaCsv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\colmenar\FutTEstadios;
use App\Entity\colmenar\FutTDetalleTipoPartido;
use App\Entity\colmenar\FutTEquipos;
use App\Entity\colmenar\FutTRivales;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartidosLigaCsvRepository extends ServiceEntityRepository
{
    private $entityManager;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntPartidosLigaCsv::class);
        $this->entityManager = $registry->getManager('colmenar');
    }
    
    public function getLocal($partidoCsv)
    {
        $result = $partidoCsv->getClubCasa()== 1031 ? 'si' : 'no';
        return $result;
    }
    
    public function getResultado($partidoCsv,$tipo)
    {
        $result = explode("-", $partidoCsv->getResultado());
        $result = preg_replace('/\s+/', '', $result[$tipo]);
        return $result;
    }
    
    public function getFecha($partidoCsv)
    {   
        $fecha = $partidoCsv->getFecha();
        $result = ($fecha !== null) ? new \DateTime($fecha->format('Y-m-d')) : null;
        return $result;
    }
    
    public function getHora($partidoCsv, $id_rival)
    {
        if($id_rival == 914) return null;
        
        $hora = $partidoCsv->getHora();
        $result = ($hora !== null) ? new \DateTime($hora->format('H:i')) : null;
        return $result;

    }
    
    public function getIdDetallePartido($partidoCsv,$entityManager)
    {
        $estadiosRepository = $entityManager->getRepository(FutTDetalleTipoPartido::class);
        
        $buscar = "J ".$partidoCsv->getJornada();
        
        $consulta = $estadiosRepository->createQueryBuilder('e')
            ->where('UPPER(e.nombreDetalle) LIKE :texto')
            ->setParameter('texto', '%'.strtoupper($buscar).'%')
            ->getQuery();

        $detallestipoPartidos = $consulta->getResult();
        $idDetalletipopartido = empty($detallestipoPartidos) ? null : $detallestipoPartidos[0];    
        return $idDetalletipopartido;

    }
    
    public function getIdEstadio($partidoCsv,$entityManager,$id_rival)
    {
        //Si el rival es el 914, es descansa, ponemos el campo como Alberto Ruiz
        $texto_a_buscar = $id_rival == 914 ? "Alberto Ruiz" : $partidoCsv->getCampo();  
        
        $estadiosRepository = $entityManager->getRepository(FutTEstadios::class);

        $consulta = $estadiosRepository->createQueryBuilder('e')
            ->where('UPPER(e.nombreEstadio) LIKE :texto')
            ->setParameter('texto', '%'.strtoupper($texto_a_buscar).'%')
            ->getQuery();

        $estadios = $consulta->getResult();
        $idEstadio = empty($estadios) ? null : $estadios[0];    
        return $idEstadio;
    }
    
    public function getCodigoPartido($partidoCsv)
    {
        $result = $partidoCsv->getCodigoPartido();
        return $result;
    }
    
    public function getCompeticionTalCual($partidoCsv)
    {
        $result = $partidoCsv->getCompeticion();
        return $result;
    }
    
    public function getGrupo($partidoCsv)
    {
        $result = $partidoCsv->getGrupo();
        return $result;
    }
    
    public function getJornada($partidoCsv)
    {
        $result = $partidoCsv->getJornada();
        return $result;
    }            
            
    
    public function getIdEquipo($partidoCsv,$entityManager,$local)
    {
        $letra = $this->getLetra($partidoCsv,$local);
        $equipoElegido = $this->getCompeticion($partidoCsv,$letra);
        
        $cod_eq_fed = ($local == 'si') ? $partidoCsv->getClubCasa() : $partidoCsv->getClubVisitante();
        $equipos = $entityManager->getRepository(FutTEquipos::class);
        $equipo = $equipos->findOneBy(['equipo' => $equipoElegido]);
        
        if (!$equipo) return null;
        return $equipo;
            
    }
    
    public function getIdRival($partidoCsv,$entityManager,$local)
    {
        //Si el casa y el visitante son 1031, es un partido entre nosotros. Sacamos el id del rival con el mismo proceso del id de nuestro equipo
        if($partidoCsv->getClubVisitante()== 1031 && $partidoCsv->getClubCasa()== 1031){
            
            $letra = $this->getLetra($partidoCsv,"no");
            $equipoElegido = $this->getCompeticion($partidoCsv,$letra);
            $cod_eq_fed = ($local == 'no') ? $partidoCsv->getClubCasa() : $partidoCsv->getClubVisitante();
            $equipos = $entityManager->getRepository(FutTEquipos::class);
            $equipo = $equipos->findOneBy(['equipo' => $equipoElegido]);
            
            if (!$equipo) return null;
            $idEquipo = $equipo->getIdRivalBis();
            
            return $idEquipo;
            
        } else {
            //Si entra aqui, el visitante es otro club, hay que buscarle.
            $letra = $this->getLetraParaID($partidoCsv,$local);
            $cod_eq_fed = ($local == 'no') ? $partidoCsv->getClubCasa() : $partidoCsv->getClubVisitante();
            
            //Si el Club casa o visitante esta vacio, es un Descansa. Devolvemos su ID de base de datos.
            if($cod_eq_fed == 0) return 914;
            
            $rivales = $entityManager->getRepository(FutTRivales::class);

            $equipo = $rivales->createQueryBuilder('e')
            ->where('e.codEqFed = :codEqFed')
            ->setParameter('codEqFed', $cod_eq_fed)
            ->getQuery()
            ->getResult();

            $filteredEquipo = array_filter($equipo, function($equipo) use ($letra) {
                return substr($equipo->getRival(), -1) === $letra;
            });

            if (!empty($filteredEquipo)) {
                $equipo = reset($filteredEquipo);
                $id_rival = $equipo->getIdRival();
                if (!$filteredEquipo) return null;
                return $id_rival;
            }

            return null;
        }

    }
    
    public function getIdRivalComoLocal($partidoCsv,$entityManager,$local)
    {
        //Solo para los casos en que juguemos los dos colmenar para poner el idRival como Local
        $letra = $this->getLetra($partidoCsv,"si");
        $equipoElegido = $this->getCompeticion($partidoCsv,$letra);
        $cod_eq_fed = ($local == 'no') ? $partidoCsv->getClubCasa() : $partidoCsv->getClubVisitante();
        $equipos = $entityManager->getRepository(FutTEquipos::class);
        $equipo = $equipos->findOneBy(['equipo' => $equipoElegido]);

        if (!$equipo) return null;
        $idEquipo = $equipo->getIdRivalBis();

        return $idEquipo;
    }
    
    private function getLetra($partidoCsv,$local){
        
        $campoBuscar = ($local == 'si') ? $partidoCsv->getEquipoCasa() : $partidoCsv->getEquipoVisitante();

        if (preg_match('/"([^"]+)"/', $campoBuscar, $matches)) {
            $letra = $matches[1];
        } else {
            $letra = "A";
        }
        return $letra;
    }
    
    private function getLetraParaID($partidoCsv,$local){
        
        $campoBuscar = ($local == 'si') ? $partidoCsv->getEquipoVisitante() : $partidoCsv->getEquipoCasa();

        if (preg_match('/"([^"]+)"/', $campoBuscar, $matches)) {
            $letra = $matches[1];
        } else {
            $letra = "A";
        }
        return $letra;
    }
    
    private function getCompeticion($partidoCsv,$letra){
        //PREBENJAMIN
        if (stripos($partidoCsv->getCompeticion(), "PREBENJAMIN") !== false) {
            $equipoElegido = "Prebenjamín ".$letra;
        //BENJAMIN
        } elseif (stripos($partidoCsv->getCompeticion(), "BENJAMIN") !== false) {
            $equipoElegido = "Benjamín ".$letra;
        //ALEVIN
        } elseif (stripos($partidoCsv->getCompeticion(), "ALEVIN") !== false) {
            if(stripos($partidoCsv->getCompeticion(), "FEMENINO") !== false){
                $equipoElegido = "Alevín ".$letra." femenino";
            } else {
                if(stripos($partidoCsv->getCompeticion(), "F-7") !== false){
                    $equipoElegido = "Alevín fútbol 7 ".$letra;
                } else {
                    $equipoElegido = "Alevín ".$letra;
                }
            }
        //INFANTIL
        } elseif (stripos($partidoCsv->getCompeticion(), "INFANTIL") !== false) {
            if(stripos($partidoCsv->getCompeticion(), "FEMENINO") !== false){
                $equipoElegido = "Infantil ".$letra." femenino";
            } else {
                if(stripos($partidoCsv->getCompeticion(), "F-7") !== false){
                    $equipoElegido = "Infantil fútbol 7 ".$letra;
                } else {
                    $equipoElegido = "Infantil ".$letra;
                }
            }
        //CADETE
        } elseif (stripos($partidoCsv->getCompeticion(), "CADETE") !== false) {
            if(stripos($partidoCsv->getCompeticion(), "FEMENINO") !== false){
                $equipoElegido = "Cadete ".$letra." femenino";
            } else {
                $equipoElegido = "Cadete ".$letra;
            }
        //JUVENIL
        } elseif (stripos($partidoCsv->getCompeticion(), "JUVENIL") !== false) {
            if(stripos($partidoCsv->getCompeticion(), "FEMENINO") !== false){
                $equipoElegido = "Juvenil ".$letra." femenino";
            } else {
                $equipoElegido = "Juvenil ".$letra;
            }
        //PRIMER EQUIPO
        } elseif (stripos($partidoCsv->getCompeticion(), "CATEGORIA PREFERENTE") !== false) {
            $equipoElegido = "1er Equipo";
        //PRIMER EQUIPO FEMENINO
        } elseif (stripos($partidoCsv->getCompeticion(), "PRIMERA DIVISION AUTONÓMICA FEMENINO") !== false or stripos($partidoCsv->getCompeticion(), "PREFERENTE FUTBOL FEMENINO") !== false) {
            $equipoElegido = "1er Equipo femenino";
        //AFICIONADO B-C
        } elseif (stripos($partidoCsv->getCompeticion(), "AFICIONADOS") !== false) {
            $equipoElegido = "Aficionado ".$letra;
        //AFICIONADO B FEMENINO
        } elseif (stripos($partidoCsv->getCompeticion(), "PRIMERA FUTBOL FEMENINO") !== false) {
            $equipoElegido = "Aficionado B femenino";
        //ALEVIN F7 DIVISION DE HONOR O AUTONOMICA
        } elseif (stripos($partidoCsv->getCompeticion(), "DIVISION DE HONOR ALEV-F7") !== false or stripos($partidoCsv->getCompeticion(), "PRIMERA DIVISION AUTONOMICA ALEV.F-7") !== false) {
            $equipoElegido = "Alevín fútbol 7 ".$letra;
        } else {
           $equipoElegido = null;
        }
        
        return $equipoElegido;
    }
}
