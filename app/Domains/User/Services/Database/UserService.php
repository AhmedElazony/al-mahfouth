<?php

namespace App\Domains\User\Services\Database;

use App\Domains\User\Models\User;
use App\Domains\User\Services\Contracts\UserServiceInterface;
use App\Http\Api\V1\Resources\User\UserResource;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    public function login(string $usernameOrEmail, string $password): array
    {
        $user = User::where('email', $usernameOrEmail)
            ->orWhere('username', $usernameOrEmail)
            ->first();

        if (! isset($user) || ! Hash::check($password, $user?->password)) {
            throw new \Exception(
                ResponseMessageEnum::INVALID_CREDENTIALS->value,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => UserResource::make($user), 'token' => $token];
    }

    public function logout(): void
    {
        auth()->user()
            ->tokens()->delete();
    }
}
