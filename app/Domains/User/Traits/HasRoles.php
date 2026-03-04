<?php

namespace App\Domains\User\Traits;

use App\Domains\User\Enums\UserRolesEnum;

trait HasRoles
{
    public function hasAdminRole(): bool
    {
        return in_array($this->role, [
            UserRolesEnum::SUPER_ADMIN->value,
            UserRolesEnum::ADMIN->value,
        ]);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRolesEnum::SUPER_ADMIN->value;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRolesEnum::STUDENT->value;
    }

    public function isTeacher(): bool
    {
        return $this->role === UserRolesEnum::TEACHER->value;
    }
}
