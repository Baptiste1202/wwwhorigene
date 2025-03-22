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
    private ?string $congelateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etagere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rack = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeConteneur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $positionConteneur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

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

    public function getCongelateur(): ?string
    {
        return $this->congelateur;
    }

    public function setCongelateur(?string $congelateur): static
    {
        $this->congelateur = $congelateur;

        return $this;
    }

    public function getEtagere(): ?string
    {
        return $this->etagere;
    }

    public function setEtagere(?string $etagere): static
    {
        $this->etagere = $etagere;

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

    public function getTypeConteneur(): ?string
    {
        return $this->typeConteneur;
    }

    public function setTypeConteneur(?string $typeConteneur): static
    {
        $this->typeConteneur = $typeConteneur;

        return $this;
    }

    public function getPositionConteneur(): ?string
    {
        return $this->positionConteneur;
    }

    public function setPositionConteneur(?string $positionConteneur): static
    {
        $this->positionConteneur = $positionConteneur;

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
}
