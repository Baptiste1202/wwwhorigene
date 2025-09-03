<?php

namespace App\Entity;

use App\Repository\PhenotypeRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PhenotypeRepository::class)]
#[Vich\Uploadable]
class Phenotype
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $technique = null;

    #[Vich\UploadableField(mapping: 'phenotype_docs', fileNameProperty: 'nom')]
    private ?File $file = null;

    #[ORM\ManyToOne(targetEntity: PhenotypeType::class)]
    private ?PhenotypeType $phenotypeType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mesure = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Strain::class, inversedBy: 'phenotype')]
    private Strain $strain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTechnique(): ?string
    {
        return $this->technique;
    }

    public function setTechnique(?string $technique): static
    {
        $this->technique = $technique;

        return $this;
    }

    public function getMesure(): ?string
    {
        return $this->mesure;
    }

    public function setMesure(?string $mesure): static
    {
        $this->mesure = $mesure;

        return $this;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;
    }

    public function getFile(): ?File
    {
        return $this->file;
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

    public function getPhenotypeType(): ?PhenotypeType
    {
        return $this->phenotypeType;
    }

    public function setPhenotypeType(?PhenotypeType $phenotypeType): self
    {
        $this->phenotypeType = $phenotypeType;
        
        return $this;
    }
}
