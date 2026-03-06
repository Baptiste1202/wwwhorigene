<?php

namespace App\Repository;

use App\Entity\Sequencing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sequencing>
 *
 * @method Sequencing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sequencing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sequencing[]    findAll()
 * @method Sequencing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SequencingRepository extends ServiceEntityRepository implements SequencingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sequencing::class);
    }
}