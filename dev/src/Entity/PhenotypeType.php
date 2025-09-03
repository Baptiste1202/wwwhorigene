<?php

namespace App\Entity;

use App\Repository\PhenotypeTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhenotypeTypeRepository::class)]
class PhenotypeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?String $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?String
    {
        return $this->type;
    }

    public function setType(String $type): static
    {
        $this->type = $type;

        return $this;
    }
}