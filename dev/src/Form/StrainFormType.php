<?php

namespace App\Form;

use App\Entity\Collec;
use App\Entity\DrugResistance;
use App\Entity\Genotype;
use App\Entity\MethodSequencing;
use App\Entity\Plasmyd;
use App\Entity\Project;
use App\Entity\Publication;
use App\Entity\Sample;
use App\Entity\Strain;
use App\Entity\Transformability;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class StrainFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        /*
            ->add('parentStrain', type:ParentFormType::class, options:[
                'label' => 'Parent Strain',
            ])
        */
            ->add('parentStrain', type:IntegerType::class, options:[
                'mapped' => false,
                'required' => false,
                'label' => 'Parent Strain ID',
            ])
            ->add('searchParent', ButtonType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-primary search-button',
                    'type' => 'button',
                ],
            ])
            ->add('nameStrain', options:[
                'label' => 'Name Strain',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('specie', options:[
                'label' => 'Specie',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('gender', options:[
                'label' => 'Gender',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Comments',
                'required' => false, 
                'empty_data' => null,
                'attr' => [
                    'rows' => 5, 
                    'maxlength' => 250, 
                    'placeholder' => 'Enter your comments here...'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false, 
                'empty_data' => null,
                'attr' => [
                    'rows' => 5, 
                    'maxlength' => 250, 
                    'placeholder' => 'Enter a detailed description...'
                ]
            ])
            ->add('genotype', type:EntityType::class, options:[
                'label' => 'Genotype',
                'class' => Genotype::class,
                'choice_label' => function (Genotype $genotype) {
                    return $genotype->getType(); 
                },
                'required' => false,   
                'placeholder' => '',  
                'empty_data' => null,
            ])
            ->add('transformability', type:CollectionType::class, options:[
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Transformability::class,  // Classe correspondant à l'entité
                    'choice_label' => function (Transformability $transformability) {
                        return $transformability->getNom(); // Méthode pour obtenir le label des choix
                    },
                    'placeholder' => '',  
                    'required' => false,
                ],
                'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
                'allow_delete' => true, // Permet de supprimer des entrées
                'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
                'label' => false,
            ])
            ->add('plasmyd', type:CollectionType::class, options:[
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Plasmyd::class,  // Classe correspondant à l'entité
                    'choice_label' => function (Plasmyd $plasmyd) {
                        return $plasmyd->getSlug(); // Méthode pour obtenir le label des choix
                    },
                    'placeholder' => '',  
                    'required' => false,
                ],
                'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
                'allow_delete' => true, // Permet de supprimer des entrées
                'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
                'label' => false,
            ])
            ->add('drugResistanceOnStrain', type:CollectionType::class, options:[
                'entry_type' => DrugOnFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,  // Permet d'ajouter dynamiquement des entrées
                'allow_delete' => true, // Permet de supprimer dynamiquement
                'by_reference' => false, // Important pour la persistance
                'prototype' => true, // Utile pour le JS
                'prototype_name' => '__name__',
                'label' => false,
            ])
            ->add('publication', type:CollectionType::class, options:[
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Publication::class,  // Classe correspondant à l'entité
                    'choice_label' => function (Publication $publication) {
                        return $publication->getSlug(); 
                    },
                    'placeholder' => '',  
                    'required' => false,
                ],
                'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
                'allow_delete' => true, // Permet de supprimer des entrées
                'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
                'label' => false,
            ])
            ->add('methodSequencing', type:CollectionType::class, options:[
                'entry_type' => SequencingFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,  // Permet d'ajouter dynamiquement des entrées
                'allow_delete' => true, // Permet de supprimer dynamiquement
                'by_reference' => false, // Important pour la persistance
                'prototype' => true, // Utile pour le JS
                'prototype_name' => '__name__',
                'label' => false,
            ])
            ->add('project', type:CollectionType::class, options:[
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Project::class,  // Classe correspondant à l'entité
                    'choice_label' => function (Project $project) {
                        return $project->getName(); 
                    },
                    'placeholder' => '',  
                    'required' => false,
                ],
                'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
                'allow_delete' => true, // Permet de supprimer des entrées
                'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
                'label' => false,
            ])
            ->add('collec', type:CollectionType::class, options:[
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Collec::class,  // Classe correspondant à l'entité
                    'choice_label' => function (Collec $collec) {
                        return $collec->getName(); 
                    },
                    'placeholder' => '',  
                    'required' => false,
                ],
                'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
                'allow_delete' => true, // Permet de supprimer des entrées
                'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
                'label' => false,
            ])
            ->add('prelevement',type:EntityType::class, options:[
                'label' => 'Sample',
                'class' => Sample::class,
                'choice_label' => function (Sample $sample) {
                    return $sample->getName(); 
                },
                'required' => false,   
                'placeholder' => '', 
                'empty_data' => null,
            ])
            ->add('room', options:[
                'label' => 'Room',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('shelf', options:[
                'label' => 'Shelf',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('rack', options:[
                'label' => 'Rack',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('fridge', options:[
                'label' => 'Fridge',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('containerType', options:[
                'label' => 'Container Type',
                'required' => false, 
                'empty_data' => null
            ])
            ->add('containerPosition', options:[
                'label' => 'Container Position',
                'required' => false, 
                'empty_data' => null
            ]);
            
    }
}