<?php

namespace App\Entity;

use App\Repository\StrainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: StrainRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Strain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameStrain = null;

    #[ORM\Column(length: 100)]
    private ?string $specie = null;

    #[ORM\Column(length: 100)]
    private ?string $gender = null;

    #[ORM\ManyToOne(targetEntity: Strain::class, inversedBy: 'childrenStrain')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Strain $parentStrain = null;

    /**
     * @var Collection<int, Strain>
     */
    #[ORM\OneToMany(targetEntity: Strain::class, mappedBy: 'parentStrain')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $childrenStrain = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Genotype::class, inversedBy: 'strain')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Genotype $genotype = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptionGenotype = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $infoGenotype = null;

    /**
     * @var Collection<int, phenotype>
     */
    #[ORM\OneToMany(targetEntity: Phenotype::class, mappedBy: 'strain', cascade:['persist', 'remove'], orphanRemoval:true)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $phenotype;

    /**
     * @var Collection<int, plasmyd>
     */
    #[ORM\ManyToMany(targetEntity: Plasmyd::class, inversedBy: 'strain')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $plasmyd;

    /**
     * @var Collection<int, drugResistanceOnStrain>
     */
    #[ORM\OneToMany(targetEntity: DrugResistanceOnStrain::class, mappedBy: 'strain', cascade:['persist', 'remove'], orphanRemoval:true)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $drugResistanceOnStrain;

    /**
     * @var Collection<int, publication>
     */
    #[ORM\ManyToMany(targetEntity: Publication::class, inversedBy: 'strain')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $publication;

    /**
     * @var Collection<int, methodSequencing>
     */
    #[ORM\OneToMany(targetEntity: MethodSequencing::class, mappedBy: 'strain', cascade:['persist', 'remove'], orphanRemoval:true)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $methodSequencing;

    /**
     * @var Collection<int, project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'strain')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $project = null;

    /**
     * @var Collection<int, collec>
     */
    #[ORM\ManyToMany(targetEntity: Collec::class, inversedBy: 'strain', orphanRemoval:false)]
    private ?Collection $collec = null;

    #[ORM\ManyToOne(targetEntity: Sample::class, inversedBy: 'strain')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Sample $prelevement = null;

    /**
     * @var Collection<int, storage>
     */
    #[ORM\OneToMany(targetEntity: Storage::class, mappedBy: 'strain', cascade:['persist', 'remove'], orphanRemoval:true)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Collection $storage = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'strain')]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $createdByName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateArchive = null;

    public function __construct()
    {
        $this->drugResistanceOnStrain = new ArrayCollection();
        $this->publication = new ArrayCollection();
        $this->phenotype = new ArrayCollection();
        $this->plasmyd = new ArrayCollection();
        $this->methodSequencing = new ArrayCollection();
        $this->project = new ArrayCollection();
        $this->collec = new ArrayCollection();
        $this->storage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id; 
        return $this;
    }

    public function getNameStrain(): ?string
    {
        return $this->nameStrain;
    }

    public function setNameStrain(?string $nameStrain): static
    {
        $this->nameStrain = $nameStrain;

        return $this;
    }

    public function getSpecie(): ?string
    {
        return $this->specie;
    }

    public function setSpecie(?string $specie): static
    {
        $this->specie = $specie;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getParentStrain(): ?Strain
    {
        return $this->parentStrain;
    }

    public function setParentStrain(?Strain $parentStrain): static
    {
        $this->parentStrain = $parentStrain;

        return $this;
    }

    public function getChildrenStrain(): ?Collection
    {
        return $this->childrenStrain;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGenotype(): ?Genotype
    {
        return $this->genotype;
    }

    public function setGenotype(?Genotype $genotype): static
    {
        $this->genotype = $genotype;

        return $this;
    }

    public function getDescriptionGenotype(): ?string
    {
        return $this->descriptionGenotype;
    }

    public function setDescriptionGenotype(?string $comment): static
    {
        $this->descriptionGenotype = $comment;

        return $this;
    }

    public function getInfoGenotype(): ?string
    {
        return $this->infoGenotype;
    }

    public function setInfoGenotype(?string $comment): static
    {
        $this->infoGenotype = $comment;

        return $this;
    }

    public function getPhenotype(): ?Collection
    {
        return $this->phenotype;
    }

    public function setPhenotype(?Phenotype $phenotype): static
    {
        $this->phenotype = $phenotype;

        return $this;
    }

    public function addPhenotype(?Phenotype $phenotype): static
    {
        if (!$this->phenotype->contains($phenotype)) {
            $this->phenotype->add($phenotype);
            $phenotype->setStrain($this);
        }

        return $this;
    }

    public function removePhenotype(?Phenotype $phenotype): static
    {
        if ($this->phenotype->removeElement($phenotype)) {
            if ($phenotype->getStrain() === $this) {
                $phenotype->setStrain(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatePhenotype(): void
    {
        foreach ($this->phenotype as $transfo) {
            $transfo->setStrain($this);
        }
    }

    public function getPlasmyd(): Collection
    {
        return $this->plasmyd ?? new ArrayCollection();
    }

    public function setPlasmyd(?Collection $plasmyd): static
    {
        $this->plasmyd = $plasmyd ?? new ArrayCollection();
        return $this;
    }

    public function addPlasmyd(?Plasmyd $plasmyd): static
    {
        if ($plasmyd && !$this->getPlasmyd()->contains($plasmyd)) {
            $this->plasmyd->add($plasmyd);
            $plasmyd->addStrain($this);
        }
        return $this;
    }

    public function removePlasmyd(?Plasmyd $plasmyd): static
    {
        if ($plasmyd && $this->getPlasmyd()->removeElement($plasmyd)) {
            $plasmyd->removeStrain($this);
        }
        return $this;
    }

    public function getDrugResistanceOnStrain(): ?Collection
    {
        return $this->drugResistanceOnStrain;
    }

    public function setDrugResistanceOnStrain(?DrugResistanceOnStrain $drugResistanceOnStrain): static
    {
        $this->drugResistanceOnStrain = $drugResistanceOnStrain;

        return $this;
    }

    public function addDrugResistanceOnStrain(?DrugResistanceOnStrain $drugResistanceOnStrain): static
    {
        if (!$this->drugResistanceOnStrain->contains($drugResistanceOnStrain)) {
            $this->drugResistanceOnStrain->add($drugResistanceOnStrain);
            $drugResistanceOnStrain->setStrain($this);
        }

        return $this;
    }

    public function removeDrugResistanceOnStrain(?DrugResistanceOnStrain $drugResistanceOnStrain): static
    {
        if ($this->drugResistanceOnStrain->removeElement($drugResistanceOnStrain)) {
            if ($drugResistanceOnStrain->getStrain() === $this) {
                $drugResistanceOnStrain->setStrain(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateDrugResistanceOnStrain(): void
    {
        foreach ($this->drugResistanceOnStrain as $drug) {
            $drug->setStrain($this);
        }
    }

    public function getPublication(): Collection
    {
        return $this->publication ?? new ArrayCollection();
    }

    public function setPublication(?Collection $publication): static
    {
        $this->publication = $publication ?? new ArrayCollection();
        return $this;
    }

    public function addPublication(?Publication $publication): static
    {
        if ($publication && !$this->getPublication()->contains($publication)) {
            $this->publication->add($publication);
            $publication->addStrain($this);
        }

        return $this;
    }

    public function removePublication(?Publication $publication): static
    {
        if ($publication && $this->getPublication()->removeElement($publication)) {
            $publication->removeStrain($this);
        }

        return $this;
    }

    public function getMethodSequencing(): ?Collection
    {
        return $this->methodSequencing;
    }

    public function setMethodSequencing(?MethodSequencing $methodSequencing): static
    {
        $this->methodSequencing = $methodSequencing;

        return $this;
    }

    public function addMethodSequencing(?MethodSequencing $methodSequencing): static
    {
        if (!$this->methodSequencing->contains($methodSequencing)) {
            $this->methodSequencing->add($methodSequencing);
            $methodSequencing->setStrain($this);
        }

        return $this;
    }

    public function removeMethodSequencing(?MethodSequencing $methodSequencing): static
    {
        if ($this->methodSequencing->removeElement($methodSequencing)) {
            $methodSequencing->setStrain($this);
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateMethodSequencing(): void
    {
        foreach ($this->methodSequencing as $method) {
            if ($method->getStrain() !== $this) {
                $method->setStrain($this); 
            }
        }
    }

    public function getProject(): ?Collection
    {
        return $this->project ?? new ArrayCollection();
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project ?? new ArrayCollection();
        return $this;
    }

    public function addProject(?Project $project): static
    {
        if ($project && !$this->getProject()->contains($project)) {
            $this->project->add($project);
            $project->addStrain($this);
        }
        return $this;
    }

    public function removeProject(?Project $project): static
    {
        if ($project && $this->getProject()->removeElement($project)) {
            $project->removeStrain($this);
        }
        return $this;
    }

    public function getCollec(): Collection
    {
        return $this->collec ?? new ArrayCollection();
    }

    public function setCollec(?Collection $collec): static
    {
        $this->collec = $collec ?? new ArrayCollection();
        return $this;
    }

    public function addCollec(?Collec $collec): static
    {
        if ($collec && !$this->getCollec()->contains($collec)) {
            $this->collec->add($collec);
            $collec->addStrain($this);
        }
        return $this;
    }
    
    public function removeCollec(?Collec $collec): static
    {
        if ($collec && $this->getCollec()->removeElement($collec)) {
            $collec->removeStrain($this);
        }
        return $this;
    }

    public function getPrelevement(): ?Sample
    {
        return $this->prelevement;
    }

    public function setPrelevement(?Sample $prelevement): static
    {
        $this->prelevement = $prelevement;

        return $this;
    }

    public function getStorage(): ?Collection
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    public function addStorage(?Storage $storage): static
    {
        if (!$this->storage->contains($storage)) {
            $this->storage->add($storage);
            $storage->setStrain($this);
        }

        return $this;
    }

    public function removeStorage(?Storage $storage): static
    {
        if ($this->storage->removeElement($storage)) {
            $storage->setStrain($this);
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateStorage(): void
    {
        foreach ($this->storage as $storage) {
            if ($storage->getStrain() !== $this) {
                $storage->setStrain($this); 
            }
        }
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getCreatedByName(): string
    {
        return $this->createdByName;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
        $this->createdByName = $createdBy->getLastname() . ' ' . $createdBy->getFirstname(); 
    }

    public function getUserCreator(): ?User
    {
        return $this->createdBy;
    }

    public function setUserCreator(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDateArchive(): ?\DateTimeInterface
    {
        return $this->dateArchive;
    }

    public function setDateArchive(?\DateTimeInterface $date): static
    {
        $this->dateArchive = $date;

        return $this;
    }

}
