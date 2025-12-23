<?php

namespace App\Domains\User\Enums;

use App\Support\Traits\HasEnumFunctions;

enum UserGendersEnum: string
{
    use HasEnumFunctions;

    case MALE = 'male';
    case FEMALE = 'female';
}
