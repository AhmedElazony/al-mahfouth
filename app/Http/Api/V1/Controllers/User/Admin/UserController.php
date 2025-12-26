<?php

namespace App\Http\Api\V1\Controllers\User\Admin;

use App\Domains\User\Services\Contracts\UserServiceInterface;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Requests\User\CreateUserRequest;
use App\Http\Api\V1\Resources\User\UserResource;
use App\Support\Enums\ResponseMessageEnum;

class UserController extends ApiController
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

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
                // $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }
}
