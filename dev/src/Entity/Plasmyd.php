<?php

namespace App\Entity;

use App\Repository\PlasmydRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PlasmydRepository::class)]
class Plasmyd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $namePlasmyd = null;

    #[ORM\Column]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, Strain>
     */
    #[ORM\ManyToMany(targetEntity: Strain::class, mappedBy: 'plasmyd', orphanRemoval: false)]
    private Collection $strain;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamePlasmyd(): ?string
    {
        return $this->namePlasmyd;
    }

    public function setNamePlasmyd(string $namePlasmyd): static
    {
        $this->namePlasmyd = $namePlasmyd;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

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
            $strain->addPlasmyd($this);
        }

        return $this;
    }
}
