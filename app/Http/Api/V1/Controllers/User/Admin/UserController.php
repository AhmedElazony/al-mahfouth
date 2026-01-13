<?php

namespace App\Http\Api\V1\Controllers\User\Admin;

use App\Domains\User\Models\User;
use App\Domains\User\Services\Contracts\UserServiceInterface;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Requests\User\CreateUserRequest;
use App\Http\Api\V1\Requests\User\UpdateUserRequest;
use App\Http\Api\V1\Resources\User\UserResource;
use App\Support\Enums\ResponseMessageEnum;

class UserController extends ApiController
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function index()
    {
        try {
            $perPage = request()->query('per_page', 15);
            $filters = request()->only(['q', 'role', 'gender']);

            $users = $this->userService->get($perPage, filters: $filters);

            return $this->paginated(
                data: $users,
                resource: UserResource::class
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function show(User $user)
    {
        try {
            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                UserResource::make($user->load(! $user->hasAdminRole() ? $user->role : []))
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());

            return $this->success(
                __(ResponseMessageEnum::ADDED_SUCCESSFULLY->value),
                UserResource::make($user)
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $user = $this->userService->update($user, $request->validated());

            return $this->success(
                __(ResponseMessageEnum::UPDATED_SUCCESSFULLY->value),
                UserResource::make($user)
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->delete($user);

            return $this->success(
                __(ResponseMessageEnum::DELETED_SUCCESSFULLY->value)
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }
}
