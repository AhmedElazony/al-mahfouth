<?php

namespace App\Domains\User\Services\Contracts;

interface UserServiceInterface
{
    public function login(string $usernameOrEmail, string $password): array;

    public function logout(): void;
}
