<?php

namespace App\Form;

use App\Entity\MethodSequencing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('description', TextareaType::class, options:[
                'label' => 'Description',
                'attr' => [
                    'rows' => 1, 
                    'maxlength' => 245, 
                    'placeholder' => 'Enter your description here...'
                ]
            ])
            ->add('comment', TextareaType::class, options:[
                'label' => 'Comments',
                'attr' => [
                    'rows' => 1, 
                    'maxlength' => 245, 
                    'placeholder' => 'Enter your comment here...'
                ]
            ]);           

            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MethodSequencing::class,
        ]);
    }
}