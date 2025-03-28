<?php

namespace App\Form;

use App\Entity\Storage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StorageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', options:[
                'label' => 'Room',
                'attr' => [
                    'placeholder' => 'Room'
                ]
            ])
            ->add('fridge', options:[
                'label' => 'Congelateur'
            ])
            ->add('shelf', options:[
                'label' => 'Etagere'
            ])
            ->add('rack', options:[
                'label' => 'Rack'
            ])
            ->add('containerType', options:[
                'label' => 'Conteneur Type'
            ])
            ->add('containerPosition', options:[
                'label' => 'Conteneur Position'
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
            'data_class' => Storage::class,
        ]);
    }
}