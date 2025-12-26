<?php

namespace App\Domains\User\Services\Database;

use App\Domains\User\Enums\UserGendersEnum;
use App\Domains\User\Enums\UserRolesEnum;
use App\Domains\User\Models\Student;
use App\Domains\User\Models\Teacher;
use App\Domains\User\Models\User;
use App\Domains\User\Services\Contracts\UserServiceInterface;
use App\Http\Api\V1\Resources\User\UserResource;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'gender' => $data['gender'] ?? UserGendersEnum::MALE->value,
                'role' => $data['role'] ?? UserRolesEnum::STUDENT->value,
            ]);

            $this->createRoleProfile(
                $user,
                $user->role ?? $data['role'],
                $data
            );

            return $user;
        });

    }

    public function login(string $usernameOrEmail, string $password): array
    {
        return DB::transaction(function () use ($usernameOrEmail, $password) {
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
        });

    }

    public function logout(): void
    {
        auth()->user()
            ->tokens()->delete();
    }

    private function createRoleProfile(User $user, string $role, array $data)
    {
        $currentUserId = auth()->id();
        match ($role) {
            UserRolesEnum::TEACHER->value => Teacher::create([
                'user_id' => $user->id,
                'created_by' => $currentUserId,
                'specialization' => $data['teacher_specialization'] ?? null,
            ]),
            UserRolesEnum::STUDENT->value => Student::create([
                'user_id' => $user->id,
                'created_by' => $currentUserId,
                'educational_stage' => $data['student_educational_stage'] ?? null,
                'begin_memorizing_at' => $data['student_begin_memorizing_at'] ?? null,
                'memorizing_completed_at' => $data['student_memorizing_completed_at'] ?? null,
            ]),
            default => null,
        };
    }
}
