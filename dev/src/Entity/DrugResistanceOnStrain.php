<?php

namespace App\Entity;

use App\Repository\DrugOnStrainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrugOnStrainRepository::class)]
class DrugResistanceOnStrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DrugResistance::class)]
    private ?DrugResistance $drugResistance;

    #[ORM\Column]
    private ?int $concentration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Strain::class, inversedBy: 'drugResistanceOnStrain')]
    private Strain $strain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDrugResistance(): ?DrugResistance
    {
        return $this->drugResistance;
    }

    public function setDrugResistance(?DrugResistance $drugResistance): static
    {
        $this->drugResistance = $drugResistance;

        return $this;
    }

    public function getConcentration(): ?int
    {
        return $this->concentration;
    }

    public function setConcentration(?int $concentration): static
    {
        $this->concentration = $concentration;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $commentaire): static
    {
        $this->comment = $commentaire;

        return $this;
    }

    public function getStrain(): Strain
    {
        return $this->strain;
    }

    public function setStrain(?Strain $strain): static
    {
        $this->strain = $strain;
        return $this;
    }



}
