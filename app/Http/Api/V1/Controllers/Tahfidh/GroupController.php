<?php

namespace App\Http\Api\V1\Controllers\Tahfidh;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Services\Contracts\GroupServiceInterface;
use App\Domains\User\Models\Student;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Requests\Tahfidh\Groups\AssignStudentRequest;
use App\Http\Api\V1\Requests\Tahfidh\Groups\StoreGroupRequest;
use App\Http\Api\V1\Requests\Tahfidh\Groups\UpdateAssignedStudentRequest;
use App\Http\Api\V1\Requests\Tahfidh\Groups\UpdateGroupRequest;
use App\Http\Api\V1\Resources\Tahfidh\GroupResource;
use App\Http\Api\V1\Resources\Tahfidh\GroupStudentResource;
use App\Support\Enums\ResponseMessageEnum;

class GroupController extends ApiController
{
    public function __construct(
        protected GroupServiceInterface $groupService,
    ) {}

    public function index()
    {
        try {
            $perPage = request()->query('per_page', 15);
            $groups = $this->groupService->get($perPage);

            return $this->paginated(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                200,
                $groups,
                GroupResource::class,
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function show(Group $group)
    {
        try {
            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                GroupResource::make($group->load('teacher')),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            $group = $this->groupService
                ->create($request->validated());

            return $this->success(
                __(ResponseMessageEnum::ADDED_SUCCESSFULLY->value),
                GroupResource::make($group->load('teacher')),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function update(Group $group, UpdateGroupRequest $request)
    {
        try {
            $updatedGroup = $this->groupService
                ->update($group, $request->validated());

            return $this->success(
                __(ResponseMessageEnum::UPDATED_SUCCESSFULLY->value),
                GroupResource::make($updatedGroup->load('teacher')),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function destroy(Group $group)
    {
        try {
            $this->groupService->delete($group);

            return $this->success(
                __(ResponseMessageEnum::DELETED_SUCCESSFULLY->value),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function getStudents(Group $group)
    {
        try {
            $students = $this->groupService
                ->getStudents($group);

            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                GroupStudentResource::collection($students),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function assignStudent(Group $group, AssignStudentRequest $request)
    {
        try {
            $students = $this->groupService
                ->assignStudent($group, $request->validated());

            return $this->success(
                __(ResponseMessageEnum::UPDATED_SUCCESSFULLY->value),
                GroupStudentResource::collection($students),
            );
        } catch (\Throwable $th) {
            dd($th->getMessage());

            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function updateStudent(Group $group, Student $student, UpdateAssignedStudentRequest $request)
    {
        try {
            $students = $this->groupService
                ->updateAssignedStudent($group, $student->user_id, $request->validated());

            return $this->success(
                __(ResponseMessageEnum::UPDATED_SUCCESSFULLY->value),
                GroupStudentResource::collection($students),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function removeStudent(Group $group, Student $student)
    {
        try {
            $this->groupService
                ->removeStudent($group, $student->user_id);

            return $this->success(
                __(ResponseMessageEnum::UPDATED_SUCCESSFULLY->value),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }
}
