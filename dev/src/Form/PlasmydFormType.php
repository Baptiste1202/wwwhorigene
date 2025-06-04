<?php

namespace App\Form;

use App\Enum\PlasmydEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class PlasmydFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('namePlasmyd', options:[
                'label' => 'Name Plasmyd'
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