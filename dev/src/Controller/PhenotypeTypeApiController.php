<?php
// src/Controller/PhenotypeTypeApiController.php

namespace App\Controller;

use App\Repository\PhenotypeTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PhenotypeTypeApiController extends AbstractController
{
    #[Route('/apiphenotypetype', name: 'apiphenotypetype', methods: ['GET'])]
    public function list(PhenotypeTypeRepository $repo): JsonResponse
    {
        $types = $repo->findBy([], ['type' => 'ASC']);

        $data = array_map(static fn($t) => [
            'id'   => $t->getId(),
            'type' => $t->getType(),
        ], $types);

        return $this->json($data);
    }
}
