<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TransformabilityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('technique', options:[
                'label' => 'Technique'
            ])
            ->add('file', VichFileType::class, options:[
                'required' => false,
                'label' => 'Mesure'
            ])
            ->add('mesure', options:[
                'label' => 'Mesure'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ]);

            
    }
}