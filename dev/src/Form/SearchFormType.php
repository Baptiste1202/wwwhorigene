<?php

namespace App\Form;

use App\Entity\DrugResistance;
use App\Entity\Genotype;
use App\Entity\Plasmyd;
use App\Entity\Project;
use App\Entity\Sample;
use App\Entity\User;
use App\Form\Model\SearchModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
        ->add('query', SearchType::class, [
            'required' => false,
            'label' => 'Name'
        ])
        ->add('plasmyd', EntityType::class, [
            'class' => Plasmyd::class,
            'required' => false,
            'choice_label' => function (Plasmyd $plasmyd) {
                return $plasmyd->getNamePlasmyd(); 
            }
        ])
        ->add('drug', EntityType::class, [
            'class' => DrugResistance::class,
            'required' => false,
            'choice_label' => function (DrugResistance $genotype) {
                return $genotype->getName(); 
            }
        ])
        ->add('genotype', EntityType::class, [
            'class' => Genotype::class,
            'required' => false,
            'choice_label' => function (Genotype $genotype) {
                return $genotype->getType(); 
            }
        ])
        ->add('project', EntityType::class, [
            'class' => Project::class,
            'required' => false,
            'choice_label' => function (Project $project) {
                return $project->getName(); 
            }
        ])
        ->add('sample', EntityType::class, [
            'class' => Sample::class,
            'required' => false,
            'choice_label' => function (Sample $sample) {
                return $sample->getName(); 
            }
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
        ->add('createdThisMonth', CheckboxType::class, [
            'required' => false,
            'label' => "New"
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