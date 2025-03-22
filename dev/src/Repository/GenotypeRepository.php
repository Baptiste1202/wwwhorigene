<?php

namespace App\Repository;

use App\Entity\Genotype;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Genotype>
 *
 * @method Genotype|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genotype|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genotype[]    findAll()
 * @method Genotype[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenotypeRepository extends ServiceEntityRepository implements GenotypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genotype::class);
    }

//    /**
//     * @return Genotype[] Returns an array of Genotype objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Genotype
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
