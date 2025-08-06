<?php

namespace App\Form\Autocomplete;

use App\Entity\Strain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class StrainAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Strain::class,

            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => [
                'id',
                'nameStrain',
                'specie',
                'gender'
            ],
            // Affiche "NomDeSouche – ID" dans la liste
            'choice_label'     => function (Strain $strain) {
                return sprintf(
                    '%s – %d',
                    $strain->getNameStrain(),
                    $strain->getId()
                );
            },

            // if the autocomplete endpoint needs to be secured
            //'security' => 'ROLE_FOOD_ADMIN',

            // ... any other normal EntityType options
            // e.g. query_builder, choice_label
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}