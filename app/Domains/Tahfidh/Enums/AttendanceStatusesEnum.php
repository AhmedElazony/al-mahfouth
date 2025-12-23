<?php

namespace App\Domains\Tahfidh\Enums;

use App\Support\Traits\HasEnumFunctions;

enum AttendanceStatusesEnum: string
{
    use HasEnumFunctions;

    case ATTENDED = 'attended';
    case ABSENT = 'absent';
    case EXCUSED = 'excused';
}
