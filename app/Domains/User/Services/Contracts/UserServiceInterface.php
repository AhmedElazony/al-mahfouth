<?php

namespace App\Domains\User\Services\Contracts;

use App\Domains\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function get(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function login(string $usernameOrEmail, string $password): array;

    public function logout(): void;
}
