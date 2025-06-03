<?php

namespace App\Entity;

use App\Repository\StorageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $room = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fridge = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shelf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rack = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $containerType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $containerPosition = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $volume = null;

    #[ORM\ManyToOne(targetEntity: Strain::class, inversedBy: 'MethodSequencing')]
    private Strain $strain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getFridge(): ?string
    {
        return $this->fridge;
    }

    public function setFridge(?string $congelateur): static
    {
        $this->fridge = $congelateur;

        return $this;
    }

    public function getShelf(): ?string
    {
        return $this->shelf;
    }

    public function setShelf(?string $etagere): static
    {
        $this->shelf = $etagere;

        return $this;
    }

    public function getRack(): ?string
    {
        return $this->rack;
    }

    public function setRack(?string $rack): static
    {
        $this->rack = $rack;

        return $this;
    }

    public function getContainerType(): ?string
    {
        return $this->containerType;
    }

    public function setContainerType(?string $typeConteneur): static
    {
        $this->containerType = $typeConteneur;

        return $this;
    }

    public function getContainerPosition(): ?string
    {
        return $this->containerPosition;
    }

    public function setContainerPosition(?string $positionConteneur): static
    {
        $this->containerPosition = $positionConteneur;

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

    public function getStrain(): Strain
    {
        return $this->strain;
    }

    public function setStrain(?Strain $strain): self
    {
        $this->strain = $strain;
        return $this;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(?string $volume): static
    {
        $this->volume = $volume;

        return $this;
    }   
}
