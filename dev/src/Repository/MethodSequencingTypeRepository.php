<?php

namespace App\Repository;

use App\Entity\MethodSequencingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MethodSequencingType>
 *
 * @method MethodSequencingType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MethodSequencingType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MethodSequencingType[]    findAll()
 * @method MethodSequencingType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MethodSequencingTypeRepository extends ServiceEntityRepository implements MethodSequencingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MethodSequencingType::class);
    }
}
