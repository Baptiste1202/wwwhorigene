<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $articleURL = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $year = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DOI = null;

    /**
     * @var Collection<int, Strain>
     */
    #[ORM\ManyToMany(targetEntity: Strain::class, mappedBy: 'publication', orphanRemoval: false)]
    private Collection $strain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleURL(): ?string
    {
        return $this->articleURL;
    }

    public function setArticleURL(string $articleURL): static
    {
        $this->articleURL = $articleURL;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getauthor(): ?string
    {
        return $this->author;
    }

    public function setauthor(string $fileName): static
    {
        $this->author = $fileName;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $fileName): static
    {
        $this->year = $fileName;

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
            $strain->addPublication($this);
        }

        return $this;
    }

    public function getDOI(): ?string
    {
        return $this->DOI;
    }

    public function setDOI(?string $DOI): static
    {
        $this->DOI = $DOI;

        return $this;
    }
}
