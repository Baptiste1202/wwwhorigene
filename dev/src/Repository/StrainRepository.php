<?php

namespace App\Repository;

use App\Entity\Strain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Strain>
 *
 * @method Strain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Strain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Strain[]    findAll()
 * @method Strain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StrainRepository extends ServiceEntityRepository implements StrainRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Strain::class);
    }

//    /**
//     * @return Strain[] Returns an array of Strain objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Strain
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
