<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordRequired = (bool) ($options['password_required'] ?? true);

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prenom',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Admin' => 'ROLE_ADMIN',
                    'Chercheur' => 'ROLE_SEARCH',
                    'Stagiaire' => 'ROLE_INTERN',
                ],
                'multiple' => true,
                'expanded' => false,
                'label'    => 'Role',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'    => 'Password',
                'mapped'   => false,
                'required' => $passwordRequired,
                'attr'     => ['autocomplete' => 'new-password'],
                'constraints' => $passwordRequired
                    ? [new Assert\NotBlank()]
                    : [], // plus de contrainte de longueur
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => User::class,
            'password_required' => true,
        ]);
    }
}
