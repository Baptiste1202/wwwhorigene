<?php

namespace App\Entity;

use App\Repository\DrugRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: DrugRepository::class)]
class DrugResistance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\OneToMany(targetEntity: DrugResistanceOnStrain::class, mappedBy: 'drugResistance', orphanRemoval: false)]
    private Collection $drugResistanceOnStrains;

    public function __construct()
    {
        $this->drugResistanceOnStrains = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDrugResistanceOnStrains(): Collection
    {
        return $this->drugResistanceOnStrains;
    }

    public function addDrugResistanceOnStrain(DrugResistanceOnStrain $drugResistanceOnStrain): static
    {
        if (!$this->drugResistanceOnStrains->contains($drugResistanceOnStrain)) {
            $this->drugResistanceOnStrains->add($drugResistanceOnStrain);
            $drugResistanceOnStrain->setDrugResistance($this);
        }

        return $this;
    }

    public function removeDrugResistanceOnStrain(DrugResistanceOnStrain $drugResistanceOnStrain): static
    {
        if ($this->drugResistanceOnStrains->removeElement($drugResistanceOnStrain)) {
            if ($drugResistanceOnStrain->getDrugResistance() === $this) {
                $drugResistanceOnStrain->setDrugResistance(null);
            }
        }

        return $this;
    }
}
