<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StorageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', options:[
                'label' => 'Room'
            ])
            ->add('congelateur', options:[
                'label' => 'Congelateur'
            ])
            ->add('etagere', options:[
                'label' => 'Etagere'
            ])
            ->add('rack', options:[
                'label' => 'Rack'
            ])
            ->add('typeConteneur', options:[
                'label' => 'Conteneur Type'
            ])
            ->add('positionConteneur', options:[
                'label' => 'Conteneur Position'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ]);
            
    }
}