<?php

namespace App\Domains\User\Enums;

use App\Support\Traits\HasEnumFunctions;

enum UserRolesEnum: string
{
    use HasEnumFunctions;

    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
}
