<?php

namespace App\Domains\Tahfidh\Enums;

use App\Support\Traits\HasEnumFunctions;

enum MemorizingAmountsEnum: string
{
    use HasEnumFunctions;

    case LESS_THAN_ONE_QUARTER = 'less_than_one_quarter';
    case ONE_QUARTER = 'one_quarter';
    case HALF_HIZB = 'half_hizb';
    case ONE_HIZB = 'one_hizb';
    case ONE_JUZ = 'one_juz';
    case MORE_THAN_ONE_JUZ = 'more_than_one_juz';
}
