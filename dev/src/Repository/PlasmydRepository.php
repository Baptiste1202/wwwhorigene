<?php

namespace App\Repository;

use App\Entity\Plasmyd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plasmyd>
 *
 * @method Plasmyd|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plasmyd|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plasmyd[]    findAll()
 * @method Plasmyd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlasmydRepository extends ServiceEntityRepository implements PlasmydRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plasmyd::class);
    }

//    /**
//     * @return Plasmyd[] Returns an array of Plasmyd objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plasmyd
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
