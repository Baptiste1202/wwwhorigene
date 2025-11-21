<?php

namespace App\Form\Model;

use App\Entity\DrugResistance;
use App\Entity\Genotype;
use App\Entity\Plasmyd;
use App\Entity\Project;
use App\Entity\Sample;
use App\Entity\User;
use App\Entity\PhenotypeType;
use App\Entity\Phenotype;

class SearchModel
{
    public function __construct(
        public ?int $id = null, 
        public ?string $query = null,
        public ?Plasmyd $plasmyd = new Plasmyd(),
        public ?DrugResistance $drug = new DrugResistance(),
        public ?Genotype $genotype = new Genotype(),
        public ?Project $project = new Project(),
        public ?Sample $sample = new Sample(),
        public ?User $user = new User(),
        public ?PhenotypeType $phenotypeType = new PhenotypeType(),
        public ?string $phenotypeMeasure = null,
        public ?string $sequencing = null,
        public ?string $specie = null,
        public ?string $gender = null,
        public ?bool $resistant = false
    )
    {
        
    }
}