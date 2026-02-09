<?php

namespace App\Domains\Tahfidh\Services\Database;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Services\Contracts\GroupService as GroupServiceContract;
use App\Domains\User\Models\Student;
use App\Support\Enums\ResponseMessageEnum;
use App\Support\Services\Database\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GroupService extends BaseService implements GroupServiceContract
{
    public function __construct()
    {
        parent::__construct(Group::class);
    }

    public function paginate(array $with = [], array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->model()->with($with);

        if (auth()->user()->isTeacher()) {
            $query->where('teacher_id', auth()->id());
        }

        return $query
			->filter($filters)
			->latest()
			->paginate($perPage, $columns);
    }

    public function create(array $data): Group
    {
        return $this->model()->create([
            ...$data,
            'is_online' => $data['is_online'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function getStudents(Group $group): Collection
    {
        return $group->students;
    }

    public function getStudent(Group $group, int $studentId): Student
    {
        $student = $group->students()
            ->with('tajweed')
            ->firstWhere('user_id', $studentId);

        if (! $student) {
            throw new \Exception(
                __(ResponseMessageEnum::NOT_FOUND->value),
                Response::HTTP_NOT_FOUND
            );
        }

        return $student;
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

    public function updateStudentProfile(Group $group, array $profileData): Student
    {
        $student = $group->students()
            ->firstWhere('user_id', $profileData['student_id']);

        if (! $student) {
            throw new \Exception(
                __(ResponseMessageEnum::NOT_FOUND->value),
                Response::HTTP_NOT_FOUND
            );
        }

        if (isset($profileData['tajweed_recitation_level']) ||
            isset($profileData['tajweed_learning_status']) ||
            isset($profileData['tajweed_notes'])) {
            $tajweedData = [
                'recitation_level' => $profileData['tajweed_recitation_level'] ?? $student->tajweed?->recitation_level,
                'learning_status' => $profileData['tajweed_learning_status'] ?? $student->tajweed?->learning_status,
                'notes' => $profileData['tajweed_notes'] ?? $student->tajweed?->notes,
            ];
            $student->tajweed()->updateOrCreate(['student_id' => $student->user_id], $tajweedData);
        }

        $student->update([
            'educational_stage' => $profileData['educational_stage'] ?? $student->educational_stage,
            'begin_memorizing_at' => $profileData['begin_memorizing_at'] ?? $student->begin_memorizing_at,
            'memorizing_completed_at' => $profileData['memorizing_completed_at'] ?? $student->memorizing_completed_at,
        ]);

        return $student->load('tajweed');
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
