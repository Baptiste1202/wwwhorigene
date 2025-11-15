<?php

namespace App\Entity;

use App\Repository\DrugOnStrainRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: DrugOnStrainRepository::class)]
#[Vich\Uploadable]
class DrugResistanceOnStrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DrugResistance::class, inversedBy: 'drugResistanceOnStrains')]
    private ?DrugResistance $drugResistance;

    #[ORM\Column]
    private ?int $concentration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Strain::class, inversedBy: 'drugResistanceOnStrain')]
    private ?Strain $strain = null;

    #[ORM\Column(nullable: true)]
    private ?bool $resistant = null;

    #[Vich\UploadableField(mapping: 'drug_docs', fileNameProperty: 'nameFile')]
    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    private ?string $nameFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?DateTime $date = null;

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

    public function isResistant(): bool
    {
        return $this->resistant;
    }

    public function setResistant(?bool $resistant): static
    {
        $this->resistant = $resistant;
        return $this;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if ($file) {
            $this->date = new \DateTime();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getNameFile(): ?string
    {
        return $this->nameFile;
    }

    public function setNameFile(?string $nom): void
    {
        $this->nameFile = $nom;

    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }



}
