<?php

namespace App\Enum;

enum PatientContextTypeEnum: string
{
    case SCREENING = 'screening';
    case INFECTION = 'infection';
}