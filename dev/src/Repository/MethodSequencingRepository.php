<?php

namespace App\Repository;

use App\Entity\MethodSequencing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MethodSequencing>
 *
 * @method MethodSequencing|null find($id, $lockMode = null, $lockVersion = null)
 * @method MethodSequencing|null findOneBy(array $criteria, array $orderBy = null)
 * @method MethodSequencing[]    findAll()
 * @method MethodSequencing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MethodSequencingRepository extends ServiceEntityRepository implements MethodSequencingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MethodSequencing::class);
    }

//    /**
//     * @return MethodSequencing[] Returns an array of MethodSequencing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MethodSequencing
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
