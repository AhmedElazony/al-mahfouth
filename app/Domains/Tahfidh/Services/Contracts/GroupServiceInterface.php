<?php

namespace App\Domains\Tahfidh\Services\Contracts;

use App\Domains\Tahfidh\Models\Group;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GroupServiceInterface
{
    public function get(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator;

    public function findBy(string $field, string $value): Group;

    public function create(array $data): Group;

    public function update(Group $group, array $data): Group;

    public function delete(Group $group): void;

    public function assignStudent(Group $group, array $studentData): void;
}
