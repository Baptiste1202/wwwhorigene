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
            ->add('file', VichFileType::class, options:[
                'required' => false,
                'label' => 'Upload File'
            ])
            ->add('name', options:[
                'label' => 'Method'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ]);           

            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MethodSequencing::class,
        ]);
    }
}