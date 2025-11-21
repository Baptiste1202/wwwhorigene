<?php

namespace App\Form\Autocomplete;

use App\Entity\Strain;
use App\Repository\StrainRepository;
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

            'choice_label' => function (Strain $strain) {
                return sprintf('%s – %d', $strain->getNameStrain(), $strain->getId());
            },

            'query_builder' => function (StrainRepository $er) {
                return $er->createQueryBuilder('s')
                    ->where('s.dateArchive IS NULL');
            },

        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}