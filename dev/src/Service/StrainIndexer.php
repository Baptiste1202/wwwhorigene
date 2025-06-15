<?php 

namespace App\Service;

use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;
use App\Entity\Strain;

class StrainIndexer
{
    public function __construct(private ObjectPersisterInterface $persister) {}

    public function index(Strain $strain): void
    {
        $this->persister->replaceMany([$strain]);
    }
}