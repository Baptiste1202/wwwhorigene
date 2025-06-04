<?php

namespace App\Form\Autocomplete;

use App\Entity\Sample;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class SampleAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Sample::class,

            // choisir les champs utilisés dans la recherche
            // si non défini, tous les champs sont utilisés
            'searchable_fields' => [
                'name'
            ],

            'choice_label' => function (Sample $sample) {
                return $sample->getName();
            }, // <- virgule ajoutée ici

            // si tu veux sécuriser l'accès à l'autocomplete
            // 'security' => 'ROLE_FOOD_ADMIN',

            // ... autres options classiques d'EntityType
            // ex: query_builder, choice_label, etc.
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
