<?php

namespace App\Form;

use App\Entity\MethodSequencing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SequencingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Name File'
                ]
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ])            
            ->add('file', VichFileType::class, options:[
                'required' => false,
                'label' => 'Fichier'
            ]);

            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MethodSequencing::class,
        ]);
    }
}