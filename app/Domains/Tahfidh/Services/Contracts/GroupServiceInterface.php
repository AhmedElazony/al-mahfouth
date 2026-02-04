<?php

namespace App\Domains\Tahfidh\Services\Contracts;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\User\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface GroupServiceInterface
{
    public function get(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator;

    public function findBy(string $field, string $value): Group;

    public function create(array $data): Group;

    public function update(Group $group, array $data): Group;

    public function delete(Group $group): void;

    public function getStudents(Group $group): Collection;

    public function getStudent(Group $group, int $studentId): Student;

    public function assignStudent(Group $group, array $studentData): Collection;

    public function updateAssignedStudent(Group $group, int $studentId, array $studentData): Collection;

    public function removeStudent(Group $group, int $studentId): void;
}
