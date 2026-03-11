<?php

namespace App\Form;

use App\Enum\HospitalSampleTypeEnum;
use App\Enum\HospitalSiteEnum;
use App\Enum\PatientContextTypeEnum;
use App\Enum\SourceEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class SampleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label' => 'Name'
            ])
            ->add('date', DateType::class, options:[
                'label' => 'Date'
            ])
            ->add('type', options:[
                'label' => 'Type'
            ])
            ->add('country', options:[
                'label' => 'Country'
            ])
            ->add('city', options:[
                'label' => 'City'
            ])
            ->add('localisation', options:[
                'label' => 'Localisation'
            ])
            ->add('underLocalisation', options:[
                'label' => 'Minor Localisation'
            ])
            ->add('gps', options:[
                'label' => 'GPS Coordinate'
            ])
            ->add('environment', options:[
                'label' => 'Environnement'
            ])
            ->add('bioSample', options:[
                'label' => 'Bio Sample',
                'required' => false
            ])
            ->add('farmLocation', options:[
                'label' => 'Farm Location',
                'required' => false
            ])
            ->add('hospitalSampleType', EnumType::class, options:[
                'class' => HospitalSampleTypeEnum::class,
                'label' => 'Hospital Sample Type',
                'required' => false
            ])
            ->add('hospitalSite', EnumType::class, options:[
                'class' => HospitalSiteEnum::class,
                'label' => 'Hospital Site',
                'required' => false
            ])
            ->add('hospitalWard', options:[
                'label' => 'Hospital Ward',
                'required' => false
            ])
            ->add('patientContextType', EnumType::class, options:[
                'class' => PatientContextTypeEnum::class,
                'label' => 'Patient Context Type',
                'required' => false
            ])
            ->add('source', EnumType::class, options:[
                'class' => SourceEnum::class,
                'label' => 'Source',
                'required' => false
            ])
            ->add('other', options:[
                'label' => 'Other Sources'
            ])
            ->add('description', options:[
                'label' => 'Description'
            ])
            ->add('comment', options:[
                'label' => 'Comments'
            ]);
    }
}