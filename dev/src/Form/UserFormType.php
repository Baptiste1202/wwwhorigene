<?php

namespace App\Form;

use App\Enum\RoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Chercheur' => 'ROLE_SEARCH',
                    'Stagiaire' => 'ROLE_INTERN',
                ],
                'multiple' => true, // Permet de sélectionner plusieurs rôles
                'expanded' => false, // Menu déroulant (false), sinon cases à cocher
                'label' => 'Roles',
            ]);

            
    }
}