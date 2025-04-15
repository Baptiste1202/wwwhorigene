<?php

namespace App\Form;

use App\Entity\DrugResistance;
use App\Entity\DrugResistanceOnStrain;
use App\Form\Autocomplete\DrugAutocompleteField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DrugOnFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('drugResistance', type:DrugAutocompleteField::class, options:[ 
                'placeholder' => 'Sélectionner une résistance',  
                'required' => true,
            ])
            ->add('concentration', options:[
                'label' => 'Concentration'
            ])
            ->add('resistant', CheckboxType::class, options:[
                'label' => 'Resistant'
            ])
            ->add('file', VichFileType::class, options:[
                'required' => false,
                'label' => 'Upload File'
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