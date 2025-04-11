<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class DrugFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label' => 'Name Drug Resistance'
            ])
            ->add('type', type: ChoiceType::class, options:[
                'choices' => [
                    'Quantitative' => 'quantitative',
                    'Qualitative' => 'qualitative'
                ]
            ])
            ->add('description', options:[
                'label' => 'Description'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ]);

            
    }
}