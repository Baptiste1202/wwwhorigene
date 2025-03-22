<?php

namespace App\Entity;

use App\Repository\TransformabilityRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: TransformabilityRepository::class)]
#[Vich\Uploadable]
class Transformability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $technique = null;

    #[Vich\UploadableField(mapping: 'transformability_docs', fileNameProperty: 'nom')]
    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mesure = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, Strain>
     */
    #[ORM\ManyToMany(targetEntity: Strain::class, mappedBy: 'transformability', orphanRemoval: true)]
    private Collection $strain;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;

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

    /**
     * @return Collection<int, Strain>
     */
    public function getStrain(): Collection
    {
        return $this->strain;
    }

    public function removeStrain(Strain $strain): void
    {
        $this->strain->removeElement($strain);
    }

    public function addStrain(Strain $strain): static
    {
        if (!$this->strain->contains($strain)) {
            $this->strain->add($strain);
            $strain->addTransformability($this);
        }

        return $this;
    }

}
