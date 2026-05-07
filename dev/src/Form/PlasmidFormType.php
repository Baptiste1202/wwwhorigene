<?php

namespace App\Form;

use App\Enum\PlasmidEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class PlasmidFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('namePlasmid', options:[
                'label' => 'Name Plasmid'
            ])
            ->add('type', type: ChoiceType::class, options:[
                'choices' => [
                    'WT' => 'wt',
                    'Synthetic' => 'synthetic'
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