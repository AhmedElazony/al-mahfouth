<?php

namespace App\Domains\Tahfidh\Enums;

use App\Support\Traits\HasEnumFunctions;

enum GradesEnum: string
{
    use HasEnumFunctions;

    case BAD = 'bad';
    case GOOD = 'good';
    case VERY_GOOD = 'very_good';
    case EXCELLENT = 'excellent';
}
