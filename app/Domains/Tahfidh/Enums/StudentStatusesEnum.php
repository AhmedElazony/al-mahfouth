<?php

namespace App\Domains\Tahfidh\Enums;

use App\Support\Traits\HasEnumFunctions;

enum StudentStatusesEnum: string
{
    use HasEnumFunctions;

    case COMMITTED = 'committed';
    case ABSENT = 'absent';
    case UNCOMMITTED = 'uncommitted';
}
