<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Strain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', IntegerType::class, [
                'label' => 'Enter Strain ID',
                'required' => true,
            ])
            ->add('searchType', ChoiceType::class, [
                'label' => 'Search for:',
                'choices' => [
                    'Parents' => 'parents',
                    'Children' => 'children',
                ],
                'expanded' => true, // Radio buttons
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
