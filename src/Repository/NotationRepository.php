<?php

namespace App\Repository;

use App\Entity\Notation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notation[]    findAll()
 * @method Notation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notation::class);
    }
}
