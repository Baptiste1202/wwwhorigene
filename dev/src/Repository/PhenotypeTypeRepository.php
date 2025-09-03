<?php

namespace App\Repository;

use App\Entity\PhenotypeType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhenotypeType>
 *
 * @method PhenotypeType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhenotypeType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhenotypeType[]    findAll()
 * @method PhenotypeType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhenotypeTypeRepository extends ServiceEntityRepository implements PhenotypeTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhenotypeType::class);
    }

//    /**
//     * @return PhenotypeType[] Returns an array of PhenotypeType objects
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

//    public function findOneBySomeField($value): ?PhenotypeType
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
