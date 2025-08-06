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
use App\Form\Autocomplete\CollecAutocompleteField;
use App\Form\Autocomplete\PlasmydAutocompleteField;
use App\Form\Autocomplete\ProjectAutocompleteField;
use App\Form\Autocomplete\PublicationAutocompleteField;
use App\Form\Autocomplete\SampleAutocompleteField;
use App\Form\Autocomplete\StrainAutocompleteField;
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
            ->add('parentStrain', type:StrainAutocompleteField::class, options:[

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
                'label' => 'Species',
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
            ->add('descriptionGenotype', TextareaType::class, options:[
                'label' => ' ',
                'required' => false, 
                'empty_data' => null,
                'attr' => [
                    'rows' => 2, 
                    'maxlength' => 250, 
                    'placeholder' => 'Description Genotype...'
                ]
            ])
            ->add('transformability', type:CollectionType::class, options:[
                'entry_type' => TransformabilityFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,  // Permet d'ajouter dynamiquement des entrées
                'allow_delete' => true, // Permet de supprimer dynamiquement
                'by_reference' => false, // Important pour la persistance
                'prototype' => true, // Utile pour le JS
                'prototype_name' => '__name__',
                'label' => false,
            ])
            ->add('plasmyd', type:CollectionType::class, options:[
                'entry_type' => PlasmydAutocompleteField::class,
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
                'entry_type' => PublicationAutocompleteField::class,
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
            ->add('storage', type:CollectionType::class, options:[
                'entry_type' => StorageFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,  // Permet d'ajouter dynamiquement des entrées
                'allow_delete' => true, // Permet de supprimer dynamiquement
                'by_reference' => false, // Important pour la persistance
                'prototype' => true, // Utile pour le JS
                'prototype_name' => '__name__',
                'label' => false,
            ])
            ->add('project', type:CollectionType::class, options:[
                'entry_type' => ProjectAutocompleteField::class,
                'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
                'allow_delete' => true, // Permet de supprimer des entrées
                'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
                'label' => false,
            ])
            ->add('collec', type:CollectionType::class, options:[
                'entry_type' => CollecAutocompleteField::class,
                'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
                'allow_delete' => true, // Permet de supprimer des entrées
                'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
                'label' => false,
            ])
            ->add('prelevement', SampleAutocompleteField::class);
            
    }
}