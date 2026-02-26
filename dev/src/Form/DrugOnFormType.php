<?php

namespace App\Form;

use App\Entity\DrugResistance;
use App\Entity\DrugResistanceOnStrain;
use App\Form\Autocomplete\DrugAutocompleteField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DrugOnFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('drugResistance', type:DrugAutocompleteField::class, options:[ 
                'placeholder' => 'Select a drug',  
                'required' => true,
                'label' => 'Drug',
            ])
            ->add('concentration', options:[
                'label' => 'Concentration',
                'attr' => [
                    'placeholder' => 'Concentration',
                ],
            ])
            ->add('concentration', options:[
                'label' => 'Concentration',
                'attr' => ['placeholder' => 'Concentration'],
            ])
            ->add('concentrationUnit', ChoiceType::class, [
                'label' => 'Unit',
                'required' => true, // ou false si tu veux
                'placeholder' => 'Select unit',
                'choices' => [
                    'µg/mL' => 'ug/mL',
                    'mg/mL' => 'mg/mL',
                    'g/mL'  => 'g/mL',
                    'ng/mL' => 'ng/mL',
                    // ajoute ce dont tu as besoin
                ],
            ])
            ->add('resistant', CheckboxType::class, options:[
                'label' => 'Resistant',
                'required' => false
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ]);

            if ($options['is_update']) {
                $builder->add('nameFile', TextType::class, [
                    'label' => 'File Name',
                    'required' => false,
                    'disabled' => true,
                ]);
            }

            $builder->add('description', TextareaType::class, options:[
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 1, 
                    'maxlength' => 245, 
                    'placeholder' => 'Enter your description here...'
                ]
            ])
            ->add('comment', TextareaType::class, options:[
                'label' => 'Comments',
                'required' => false,
                'attr' => [
                    'rows' => 1, 
                    'maxlength' => 245, 
                    'placeholder' => 'Enter your comment here...'
                ]
            ])
            ->add('file', VichFileType::class, options:[
                'required' => false,
                'label' => 'Upload File',
                'download_uri' => false,
                'allow_delete' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DrugResistanceOnStrain::class,
            'is_update' => false,
        ]);

        $resolver->setDefined('is_update');
    }
}