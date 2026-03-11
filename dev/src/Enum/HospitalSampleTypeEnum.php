<?php

namespace App\Enum;

enum HospitalSampleTypeEnum: string
{
    case WARD_ENVIRONMENT = 'ward_environment';
    case PATIENT = 'patient';
}