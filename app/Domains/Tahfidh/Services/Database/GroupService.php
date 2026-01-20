<?php

namespace App\Domains\Tahfidh\Services\Database;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Services\Contracts\GroupServiceInterface;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GroupService implements GroupServiceInterface
{
    public function get(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator
    {
        return Group::with('teacher')->latest()->paginate($perPage, $columns);
    }

    public function findBy(string $field, string $value): Group
    {
        $group = Group::firstWhere($field, $value);

        if (! $group) {
            throw new \Exception(
                __(ResponseMessageEnum::NOT_FOUND->value),
                Response::HTTP_NOT_FOUND
            );
        }

        return $group;
    }

    public function create(array $data): Group
    {
        return Group::create([
            ...$data,
            'is_online' => $data['is_online'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(Group $group, array $data): Group
    {
        $group->update($data);

        return $group->refresh();
    }

    public function delete(Group $group): void
    {
        $group->delete();
    }

    public function getStudents(Group $group): Collection
    {
        return $group->students;
    }

    public function assignStudent(Group $group, array $studentData): Collection
    {
        return DB::transaction(function () use ($group, $studentData) {
            if ($group->students()->where('student_id', $studentData['student_id'])->exists()) {
                throw new \Exception(
                    __(ResponseMessageEnum::ALREADY_EXISTS->value),
                    Response::HTTP_CONFLICT
                );
            }

            $group->students()->attach($studentData['student_id'], [
                'student_status' => $studentData['student_status'] ?? null,
                'memorizing_amount' => $studentData['memorizing_amount'],
                'joined_at' => now(),
            ]);

            return $group->students;
        });
    }

    public function updateAssignedStudent(Group $group, int $studentId, array $studentData): Collection
    {
        return DB::transaction(function () use ($group, $studentId, $studentData) {
            if (! $group->students()->where('student_id', $studentId)->exists()) {
                throw new \Exception(
                    __(ResponseMessageEnum::NOT_FOUND->value),
                    Response::HTTP_NOT_FOUND
                );
            }

            $group->students()->updateExistingPivot($studentId, [
                'student_status' => $studentData['student_status'] ?? null,
                'memorizing_amount' => $studentData['memorizing_amount'],
            ]);

            return $group->students;
        });
    }

    public function removeStudent(Group $group, int $studentId): void
    {
        DB::transaction(function () use ($group, $studentId) {
            if (! $group->students()->where('student_id', $studentId)->exists()) {
                throw new \Exception(
                    __(ResponseMessageEnum::NOT_FOUND->value),
                    Response::HTTP_NOT_FOUND
                );
            }

            $group->students()->detach($studentId);
        });
    }
}
