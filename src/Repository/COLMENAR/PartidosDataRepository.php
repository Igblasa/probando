<?php

namespace App\Repository\COLMENAR;

use App\Entity\colmenar\IntPartidosData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartidosDataRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, IntPartidosData::class);
         $this->entityManager = $registry->getManager('colmenar');
    }
    
    public function findUniqueEquipos()
    {
        return $this->entityManager->createQueryBuilder()
            ->select('pd.equipo')
            ->from(IntPartidosData::class, 'pd')
            ->distinct()
            ->orderBy('pd.equipo', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    public function findUniqueRivales()
    {
         return $this->entityManager->createQueryBuilder()
            ->select('DISTINCT p.rival')
            ->from(IntPartidosData::class, 'p') // Especificar la entidad y su alias
            ->orderBy('p.rival', 'ASC') // Ordenar los rivales por nombre de forma ascendente (ASC)
            ->getQuery()
            ->getResult();
    }
    
    public function findPartidosFiltrados($equiposSeleccionados, $rivalesSeleccionados, $fechaInicio, $fechaFin, $limiteRegistros)
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(IntPartidosData::class, 'p');

        // Agregar condiciones para evitar partidos con campos vacíos
        $qb->andWhere('p.resultadoLocal IS NOT NULL')
           ->andWhere('p.resultadoVisitante IS NOT NULL')
           ->andWhere('p.local IS NOT NULL');

        if (!empty($equiposSeleccionados)) {
            $qb->andWhere($qb->expr()->in('p.equipo', ':equiposSeleccionados'))
                ->setParameter('equiposSeleccionados', $equiposSeleccionados);
        }

        if (!empty($rivalesSeleccionados)) {
            $qb->andWhere($qb->expr()->in('p.rival', ':rivalesSeleccionados'))
                ->setParameter('rivalesSeleccionados', $rivalesSeleccionados);
        }

        if (!empty($fechaInicio)) {
            $fechaInicio = new \DateTime($fechaInicio);
            $fechaInicio->setTime(0, 0, 0); // Establecer la hora a las 00:00:00
            $qb->andWhere('p.fecha >= :fechaInicio')
                ->setParameter('fechaInicio', $fechaInicio);
        }

        if (!empty($fechaFin)) {
            $fechaFin = new \DateTime($fechaFin);
            $fechaFin->setTime(23, 59, 59); // Establecer la hora a las 23:59:59
            $qb->andWhere('p.fecha <= :fechaFin')
                ->setParameter('fechaFin', $fechaFin);
        }

        // Limitar a los registros solicitados
        if (!empty($limiteRegistros)) {
            $qb->setMaxResults($limiteRegistros);
        }

        $partidos = $qb->getQuery()->getResult();

        // Formatear las fechas y horas antes de devolver los resultados
        $formattedPartidos = [];
        foreach ($partidos as $partido) {
            $resultadoLocal = $partido->getResultadoLocal();
            $resultadoVisitante = $partido->getResultadoVisitante();
            $local = $partido->getLocal();

            if (
                $resultadoLocal !== null && $resultadoLocal !== ''
                && $resultadoVisitante !== null && $resultadoVisitante !== ''
                && $local !== null && $local !== ''
            ) {
                $formattedPartidos[] = [
                    'fecha' => $partido->getFecha()->format('Y-m-d'),
                    'equipo' => $partido->getEquipo(),
                    'rival' => $partido->getRival(),
                    'resultadoLocal' => $partido->getResultadoLocal(),
                    'resultadoVisitante' => $partido->getResultadoVisitante(),
                    'horario' => $partido->getHorario() ? $partido->getHorario()->format('H:i') : '',
                    'local' => $partido->getLocal(),
                    'nombreEstadio' => $partido->getNombreEstadio(),
                    'idPartido' => $partido->getIdPartido(),
                ];
            }
        }

        return $formattedPartidos;
    }
}
