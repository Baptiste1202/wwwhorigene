<?php

namespace App\Form\Model;

use App\Entity\DrugResistance;
use App\Entity\Genotype;
use App\Entity\Plasmyd;
use App\Entity\Project;
use App\Entity\Sample;
use App\Entity\User;

class SearchModel
{
    public function __construct(
        public ?string $query = null,
        public ?Plasmyd $plasmyd = new Plasmyd(),
        public ?DrugResistance $drug = new DrugResistance(),
        public ?Genotype $genotype = new Genotype(),
        public ?Project $project = new Project(),
        public ?Sample $sample = new Sample(),
        public ?User $user = new User(),
        public ?string $specie = null,
        public ?string $gender = null,
        public ?bool $createdThisMonth = false
    )
    {
        
    }
}