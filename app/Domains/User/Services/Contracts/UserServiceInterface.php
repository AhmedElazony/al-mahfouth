<?php

namespace App\Domains\User\Services\Contracts;

use App\Domains\User\Models\User;

interface UserServiceInterface
{
    public function create(array $data): User;

    public function login(string $usernameOrEmail, string $password): array;

    public function logout(): void;
}
