<?php

namespace App\Form;

use App\Entity\DrugResistance;
use App\Entity\DrugResistanceOnStrain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DrugOnFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('drugResistance', type:EntityType ::class, options:[
                'class' => DrugResistance::class,  
                'choice_label' => function (DrugResistance $drugResistance) {
                    return $drugResistance->getName();  
                },
                'placeholder' => 'Sélectionner une résistance',  
                'required' => true,
            ])
            ->add('concentration', options:[
                'label' => 'Concentration'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ]);   
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DrugResistanceOnStrain::class,
        ]);
    }
}