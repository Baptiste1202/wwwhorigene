<?php

namespace App\Form;

use App\Entity\MethodSequencing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'label' => 'Upload File',
                'allow_delete' => true,
                'download_uri' => false,
            ]); 

            if ($options['is_update']) {
                $builder->add('nameFile', TextType::class, [
                    'label' => 'File Name',
                    'required' => false,
                    'disabled' => true,
                ]);
            }

            $builder->add('name', options:[
                'label' => 'Method'
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text', 
                'required' => false,
            ])
            ->add('description', TextareaType::class, options:[
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 1, 
                    'maxlength' => 245, 
                    'placeholder' => 'Enter your description here...'
                ]
            ])
            ->add('comment', TextareaType::class, options:[
                'label' => 'Comments',
                'required' => false,
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
            'is_update' => false,
        ]);

        $resolver->setDefined('is_update');
    }
}