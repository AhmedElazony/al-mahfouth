<?php

namespace App\Domains\Tahfidh\Enums;

use App\Support\Traits\HasEnumFunctions;

enum EducationalStagesEnum: string
{
    use HasEnumFunctions;

    case NO_SCHOOL = 'no_school';
    case PRIMARY = 'primary_school';
    case PREPARATORY = 'preparatory_school';
    case SECONDARY = 'secondary_school';
    case UNIVERSITY = 'university_stage';
    case GRADUATE = 'graduate';
}
