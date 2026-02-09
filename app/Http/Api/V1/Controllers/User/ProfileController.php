<?php

namespace App\Http\Api\V1\Controllers\User;

use App\Domains\User\Services\Contracts\UserService;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Requests\User\UpdateProfileRequest;
use App\Http\Api\V1\Resources\User\UserResource;
use App\Support\Enums\ResponseMessageEnum;

class ProfileController extends ApiController
{
    public function __construct(protected UserService $userService) {}

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = $this->userService->updateProfile($request->validated());

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
}
