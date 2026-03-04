<?php

namespace App\Http\Api\V1\Controllers\User;

use App\Domains\User\Services\Contracts\UserService;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Requests\User\Auth\LoginRequest;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\JsonResponse;

class AuthController extends ApiController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->userService->login(
                $request->input('username_or_email'),
                $request->input('password')
            );

            return $this->success(__(ResponseMessageEnum::LOGIN_SUCCESSFUL->value), $data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th instanceof \Exception ? $th->getCode() : 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->userService->logout();

            return $this->success(__(ResponseMessageEnum::LOGOUT_SUCCESSFUL->value));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th instanceof \Exception ? $th->getCode() : 500);
        }
    }
}
