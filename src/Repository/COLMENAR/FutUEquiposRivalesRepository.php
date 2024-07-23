<?php

namespace App\Repository\COLMENAR;

use App\Entity\colmenar\FutUEquiposRivales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FutUEquiposRivalesRepository extends ServiceEntityRepository
{    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FutUEquiposRivales::class);
    }
    
}
