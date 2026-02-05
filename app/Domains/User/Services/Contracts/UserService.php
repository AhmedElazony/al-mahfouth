<?php

namespace App\Domains\User\Services\Contracts;

use App\Domains\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserService
{
    public function get(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): void;

    public function login(string $usernameOrEmail, string $password): array;

    public function logout(): void;
}
