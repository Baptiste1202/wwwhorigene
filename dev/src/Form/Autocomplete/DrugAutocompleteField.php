<?php

namespace App\Form\Autocomplete;

use App\Entity\DrugResistance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class DrugAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => DrugResistance::class,

            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => [
                'name',
            ],
            'choice_label' => function (DrugResistance $drug) {
                return $drug->getName(); 
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