<?php

namespace App\Domains\User\Services\Contracts;

use App\Domains\User\Models\User;
use App\Support\Services\Contracts\BaseService;
use Illuminate\Support\Collection;

interface UserService extends BaseService
{
    public function updateProfile(array $data): User;

    public function getStudentGroups(): Collection;

    public function login(string $usernameOrEmail, string $password): array;

    public function logout(): void;
}
