<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class SampleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label' => 'Name'
            ])
            ->add('date', DateType::class, options:[
                'label' => 'Date'
            ])
            ->add('type', options:[
                'label' => 'Type'
            ])
            ->add('country', options:[
                'label' => 'Country'
            ])
            ->add('city', options:[
                'label' => 'City'
            ])
            ->add('localisation', options:[
                'label' => 'Localisation'
            ])
            ->add('underLocalisation', options:[
                'label' => 'Minor Localisation'
            ])
            ->add('gps', options:[
                'label' => 'GPS Coordinate'
            ])
            ->add('environment', options:[
                'label' => 'Environnement'
            ])
            ->add('other', options:[
                'label' => 'Other Sources'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ])
            ->add('user', options:[
                'label' => 'User'
            ]);
            
    }
}