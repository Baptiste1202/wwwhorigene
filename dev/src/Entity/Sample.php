<?php

namespace App\Entity;

use App\Repository\SampleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SampleRepository::class)]
class Sample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $underLocalisation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gps = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $environment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $other = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'samples')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bioSample = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $farmLocation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hospitalSampleType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hospitalSite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hospitalWard = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $patientContextType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    /**
     * @var Collection<int, Strain>
     */
    #[ORM\OneToMany(targetEntity: Strain::class, mappedBy: 'prelevement', orphanRemoval: true)]
    private Collection $strain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id; 
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getUnderLocalisation(): ?string
    {
        return $this->underLocalisation;
    }

    public function setUnderLocalisation(?string $underLocalisation): static
    {
        $this->underLocalisation = $underLocalisation;

        return $this;
    }

    public function getGPS(): ?string
    {
        return $this->gps;
    }

    public function setGPS(?string $GPS): static
    {
        $this->gps = $GPS;

        return $this;
    }

    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    public function setEnvironment(?string $environment): static
    {
        $this->environment = $environment;

        return $this;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function setOther(?string $other): static
    {
        $this->other = $other;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
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

    /**
     * @return Collection<int, Strain>
     */
    public function getStrain(): Collection
    {
        return $this->strain;
    }

    public function getBioSample(): ?string
    {
        return $this->bioSample;
    }

    public function setBioSample(?string $bioSample): static
    {
        $this->bioSample = $bioSample;
        return $this;
    }

    public function getFarmLocation(): ?string
    {
        return $this->farmLocation;
    }

    public function setFarmLocation(?string $farmLocation): static
    {
        $this->farmLocation = $farmLocation;
        return $this;
    }

    public function getHospitalSampleType(): ?string
    {
        return $this->hospitalSampleType;
    }

    public function setHospitalSampleType(?string $hospitalSampleType): static
    {
        $this->hospitalSampleType = $hospitalSampleType;
        return $this;
    }

    public function getHospitalSite(): ?string
    {
        return $this->hospitalSite;
    }

    public function setHospitalSite(?string $hospitalSite): static
    {
        $this->hospitalSite = $hospitalSite;
        return $this;
    }

    public function getHospitalWard(): ?string
    {
        return $this->hospitalWard;
    }

    public function setHospitalWard(?string $hospitalWard): static
    {
        $this->hospitalWard = $hospitalWard;
        return $this;
    }

    public function getPatientContextType(): ?string
    {
        return $this->patientContextType;
    }

    public function setPatientContextType(?string $patientContextType): static
    {
        $this->patientContextType = $patientContextType;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;
        return $this;
    }

}
