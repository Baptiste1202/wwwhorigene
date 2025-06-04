<?php

namespace App\Repository;

use App\Entity\FileSequencing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FileSequencing>
 *
 * @method FileSequencing|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileSequencing|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileSequencing[]    findAll()
 * @method FileSequencing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileSequencingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileSequencing::class);
    }

//    /**
//     * @return FileSequencing[] Returns an array of FileSequencing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FileSequencing
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
