<?php

namespace App\Repository\COLMENAR;

use App\Entity\colmenar\FutTCronicas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FutTCronicas|null find($id, $lockMode = null, $lockVersion = null)
 * @method FutTCronicas|null findOneBy(array $criteria, array $orderBy = null)
 * @method FutTCronicas[]    findAll()
 * @method FutTCronicas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FutTCronicasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FutTCronicas::class);
    }
}