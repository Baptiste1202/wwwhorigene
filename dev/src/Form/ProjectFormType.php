<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label' => 'Project Name'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ]);
            
    }
}