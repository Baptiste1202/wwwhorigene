<?php

namespace App\Form;

use App\Entity\DrugResistance;
use App\Entity\Genotype;
use App\Entity\Sequencing;
use App\Entity\Plasmyd;
use App\Entity\Project;
use App\Entity\Sample;
use App\Entity\User;
use App\Entity\PhenotypeType;
use App\Form\Autocomplete\PlasmydAutocompleteField;
use App\Form\Autocomplete\DrugAutocompleteField;
use App\Form\Autocomplete\ProjectAutocompleteField;
use App\Form\Autocomplete\SampleAutocompleteField;
use App\Form\Model\SearchModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
        ->add('id', SearchType::class, [
            'required' => false,
            'label' => 'ID'
        ])
        ->add('query', SearchType::class, [
            'required' => false,
            'label' => 'Name'
        ])
        ->add('plasmyd', PlasmydAutocompleteField::class, [
            'required' => false,
        ])
        // ->add('plasmyd', type:CollectionType::class, options:[
        //         'entry_type' => PlasmydAutocompleteField::class,
        //         'allow_add' => true,   // Permet d'ajouter des entrées dynamiquement
        //         'allow_delete' => true, // Permet de supprimer des entrées
        //         'by_reference' => false, // Nécessaire pour éviter les problèmes avec les relations bidirectionnelles
        //         'label' => false,
        // ])
        ->add('drug', DrugAutocompleteField::class, [
            'required' => false,
        ])
        ->add('phenotypeType', EntityType::class, [
            'class'        => PhenotypeType::class,
            'required'     => false,
            'label'        => 'Phenotype type',
            'choice_label' => function (PhenotypeType $pt) {
                return $pt->getType();
            },
        ])
        ->add('phenotypeMeasure', ChoiceType::class, [
            'required'   => false,
            'label'      => 'Phenotype measure',
            'placeholder'=> 'all',
            'choices'    => [
                'Poor'       => 'poor',
                'Average'    => 'average',
                'Good'       => 'good',
                'Very Good'  => 'very_good',
            ],
        ])
        ->add('genotype', EntityType::class, [
            'class' => Genotype::class,
            'required' => false,
            'choice_label' => function (Genotype $genotype) {
                return $genotype->getType(); 
            }
        ])
        ->add('project', ProjectAutocompleteField::class, [
            'required' => false,
        ])
        ->add('sample', SampleAutocompleteField::class, [
            'required' => false,
        ])
        ->add('user', EntityType::class, [
            'class' => User::class,
            'required' => false,
            'choice_label' => function (User $user) {
                return $user->getFirstname(). ' '. $user->getLastname(); 
            }
        ])
        ->add('specie', SearchType::class, [ 
            'required' => false,
        ])
        ->add('gender', SearchType::class, [ 
            'required' => false,
        ])
        ->add('sequencing', SearchType::class, [ 
            'required' => false,
            'label' => 'Sequencing Type file',
            'attr' => [
                'placeholder' => 'png, fastq...'
            ]
        ])
        ->add('resistant', ChoiceType::class, [
            'required' => false,
            'label' => "Résistance",
            'placeholder' => 'all',
            'choices' => [
                'Résistant' => true,
                'Sensible' => false,
            ],
        ])
        ->setMethod('GET');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => SearchModel::class,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix() : string
    {
        return ''; 
    }
}