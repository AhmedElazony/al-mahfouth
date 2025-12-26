<?php

namespace App\Http\Api\V1\Controllers\User\Admin;

use App\Domains\User\Services\Contracts\UserServiceInterface;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Requests\User\CreateUserRequest;
use App\Http\Api\V1\Resources\User\UserResource;
use App\Support\Enums\ResponseMessageEnum;
use App\Support\Traits\WithPagination;

class UserController extends ApiController
{
    use WithPagination;

    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function getUsers()
    {
        try {
            $perPage = request()->query('per_page', 15);

            $users = $this->userService->get($perPage);

            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                UserResource::collection($users->items()),
                200,
                $this->returnPaginated($users)
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function createUser(CreateUserRequest $request)
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
}
