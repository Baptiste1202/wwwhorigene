<?php

namespace App\Form;

use App\Entity\MethodSequencing;
use App\Entity\MethodSequencingType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('methodSequencingType', type: EntityType::class, options:[
                'label' => 'Sequencing Method',
                'class' => MethodSequencingType::class,
                'choice_label' => 'name',
                'required' => true,   
                'placeholder' => '',  
                'empty_data' => null,
            ])

            ->add('date', DateType::class, [
                'widget' => 'single_text', 
                'required' => false,
            ]);

            if ($options['is_update']) {
                $builder->add('nameFile', TextType::class, [
                    'label' => 'File Name',
                    'required' => false,
                    'disabled' => true,
                ]);
            }

            $builder->add('description', TextareaType::class, options:[
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
            ])
            ->add('file', VichFileType::class, options:[
                'required' => false,
                'label' => 'Upload File',
                'download_uri' => false,
                'allow_delete' => false,
                'attr' => [
                    'accept' => '*/*', // Accepte tous les types de fichiers
                ],
            ]); ;           

            
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