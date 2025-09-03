<?php

namespace App\Repository;

use App\Entity\Phenotype;
use App\Entity\Transformability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transformability>
 *
 * @method Transformability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transformability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transformability[]    findAll()
 * @method Transformability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhenotypeRepository extends ServiceEntityRepository implements TransformabilityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phenotype::class);
    }

//    /**
//     * @return Transformability[] Returns an array of Transformability objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transformability
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
