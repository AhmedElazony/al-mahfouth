<?php

namespace App\Domains\Tahfidh\Services\Database;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Services\Contracts\GroupServiceInterface;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GroupService implements GroupServiceInterface
{
    public function get(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator
    {
        return Group::paginate($perPage, $columns);
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
        return Group::create($data);
    }

    public function update(Group $group, array $data): Group
    {
        $group->update($data);

        return $group->fresh();
    }

    public function delete(Group $group): void
    {
        $group->delete();
    }

    public function assignStudent(Group $group, array $studentData): void
    {
        DB::transaction(function () use ($group, $studentData) {
            if ($group->students()->where('student_id', $studentData['student_id'])->exists()) {
                throw new \Exception(
                    __(ResponseMessageEnum::ALREADY_EXISTS->value),
                    Response::HTTP_CONFLICT
                );
            }

            $pivotData = [];
            foreach ($studentData as $data) {
                $pivotData[$data['student_id']] = [
                    'student_status' => $data['student_status'],
                    'is_online' => $data['is_online'],
                    'memorizing_amount' => $data['memorizing_amount'],
                    'joined_at' => $data['joined_at'],
                ];
            }

            $group->students()->attach($pivotData);
        });
    }
}
