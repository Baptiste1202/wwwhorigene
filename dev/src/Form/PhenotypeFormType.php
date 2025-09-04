<?php

namespace App\Form;

use App\Entity\Phenotype;
use App\Entity\PhenotypeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PhenotypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phenotypeType',type:EntityType::class, options:[
                'label' => 'Type',
                'class' => PhenotypeType::class,
                'choice_label' => function (PhenotypeType $phenotypetype) {
                    return $phenotypetype->getType(); 
                },
                'required' => true,   
                'placeholder' => '',  
                'empty_data' => null,
            ])
            ->add('technique', type: TextType::class, options: [
                'label'    => 'Technique',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Ex. PCR, MIC, etc.'
                ],
            ])
            ->add('mesure', ChoiceType::class, [
                'label' => 'Measure',
                'choices' => [
                    'Poor' => 'poor',
                    'Average' => 'average',
                    'Good' => 'good',
                    'Very Good' => 'very_good',
                ],
                'placeholder' => 'Select a measure',
                'required' => true,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text', 
                'required' => false,
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
            'data_class' => Phenotype::class,
        ]);
    }
}