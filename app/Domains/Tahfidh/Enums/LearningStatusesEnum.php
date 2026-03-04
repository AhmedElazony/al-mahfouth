<?php

namespace App\Domains\Tahfidh\Enums;

use App\Support\Traits\HasEnumFunctions;

enum LearningStatusesEnum: string
{
    use HasEnumFunctions;

    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
}
