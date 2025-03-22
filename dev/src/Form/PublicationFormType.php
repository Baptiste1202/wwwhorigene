<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PublicationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('articleUrl', options:[
                'label' => 'URL'
            ])
            ->add('title', options:[
                'label' => 'Title'
            ])
            ->add('autor', options:[
                'label' => 'Autor'
            ])
            ->add('year', options:[
                'label' => 'Year'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ]);

            
    }
}