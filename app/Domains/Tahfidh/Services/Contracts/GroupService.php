<?php

namespace App\Domains\Tahfidh\Services\Contracts;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\User\Models\Student;
use App\Support\Services\Contracts\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface GroupService extends BaseService
{
    public function getStudents(Group $group): Collection;

    public function getStudent(Group $group, int $studentId): Student;

    public function assignStudent(Group $group, array $studentData): Collection;

    public function updateAssignedStudent(Group $group, int $studentId, array $studentData): Collection;

    public function updateStudentProfile(Group $group, array $profileData): Student;

    public function removeStudent(Group $group, int $studentId): void;
}
