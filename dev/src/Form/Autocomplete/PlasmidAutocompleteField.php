<?php

namespace App\Form\Autocomplete;

use App\Entity\Collec;
use App\Entity\Plasmid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PlasmidAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Plasmid::class,

            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => [
                'namePlasmid',
            ],
            'choice_label' => function (Plasmid $plasmid) {
                return $plasmid->getNamePlasmid(); 
            }

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