<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CollecFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label' => 'Name Collection'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ]);
            
    }
}