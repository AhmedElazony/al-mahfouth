<?php

namespace App\Domains\User\Services\Contracts;

use App\Support\Services\Contracts\BaseService;

interface UserService extends BaseService
{
    public function login(string $usernameOrEmail, string $password): array;

    public function logout(): void;
}
