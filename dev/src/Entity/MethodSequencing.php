<?php

namespace App\Entity;

use App\Repository\MethodSequencingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MethodSequencingRepository::class)]
#[ORM\Table(name: "sequencing")]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class MethodSequencing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: MethodSequencingType::class, cascade: ['persist'])]
    private ?MethodSequencingType $name = null;
    
    private ?string $pendingNameString = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[Vich\UploadableField(mapping: 'sequencing_docs', fileNameProperty: 'nameFile', size: 'sizeFile')]
    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    private ?string $nameFile = null;

    #[ORM\Column(nullable: true)]
    private ?int $sizeFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $typeFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Strain::class, inversedBy: 'MethodSequencing')]
    private Strain $strain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name?->getName();
    }

    public function setName(string|MethodSequencingType|null $name): static
    {
        if (is_string($name)) {
            // Stocker temporairement le string, sera résolu dans prePersist/preUpdate
            $this->pendingNameString = $name;
            // Si on a déjà un objet MethodSequencingType avec ce nom, on le garde
            if ($this->name === null || $this->name->getName() !== $name) {
                $methodSequencingType = new MethodSequencingType();
                $methodSequencingType->setName($name);
                $this->name = $methodSequencingType;
            }
        } else {
            $this->name = $name;
            $this->pendingNameString = null;
        }

        return $this;
    }

    public function getMethodSequencingType(): ?MethodSequencingType
    {
        return $this->name;
    }

    public function setMethodSequencingType(?MethodSequencingType $name): static
    {
        $this->name = $name;
        $this->pendingNameString = null;

        return $this;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if ($file) {
            // Force VichUploader à détecter le changement en mettant à jour la date
            // avec des microsecondes pour garantir un changement
            $this->date = new \DateTime('now');
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

    public function getTypeFile(): ?string
    {
        return $this->typeFile;
    }

    public function setTypeFile(?string $type): void
    {
        $this->typeFile = $type;

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

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSizeFile(): ?int
    {
        return $this->sizeFile;
    }

    public function setSizeFile(?int $size): void
    {
        $this->sizeFile = $size;
    }

    /**
     * Recalcule automatiquement le type de fichier à partir du nom de fichier
     */
    private function updateFileType(): void
    {
        if ($this->file !== null) {
            // Si c'est un UploadedFile, on utilise getClientOriginalName()
            if ($this->file instanceof UploadedFile) {
                $filename = $this->file->getClientOriginalName();
            } else {
                // Sinon, on utilise getFilename()
                $filename = $this->file->getFilename();
            }
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $this->typeFile = $extension;
        } elseif ($this->nameFile !== null) {
            // Si pas de fichier File mais qu'on a un nameFile (cas d'édition)
            $extension = pathinfo($this->nameFile, PATHINFO_EXTENSION);
            $this->typeFile = $extension;
        }
    }

    /**
     * Callback Doctrine : recalcule le type avant la persistence
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateFileTypeOnSave(): void
    {
        $this->updateFileType();
    }

}
