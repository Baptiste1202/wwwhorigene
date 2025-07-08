<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prenom'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Chercheur' => 'ROLE_SEARCH',
                    'Stagiaire' => 'ROLE_INTERN',
                ],
                'multiple' => true, 
                'expanded' => false, 
                'label' => 'Role',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Verifie',
                'required' => false,
            ]);
    }
}
