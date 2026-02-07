<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MethodSequencingTypeRepository;

#[ORM\Entity(repositoryClass: MethodSequencingTypeRepository::class)]
class MethodSequencingType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?String $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?String
    {
        return $this->name;
    }

    public function setName(String $name): static
    {
        $this->name = $name;

        return $this;
    }
}