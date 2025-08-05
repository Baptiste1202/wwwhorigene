<?php

namespace App\Form;

use App\Entity\Transformability;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TransformabilityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('technique', options:[
                'label' => 'Technique',
                'attr' => [
                    'placeholder' => 'Technique'
                ]
            ])
            ->add('mesure', ChoiceType::class, [
                'label' => 'Measure',
                'choices' => [
                    'Poor' => 'poor',
                    'Average' => 'average',
                    'Good' => 'good',
                    'Very Good' => 'very_good'
                ],
                'placeholder' => 'Select a measure'
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
            'data_class' => Transformability::class,
        ]);
    }
}