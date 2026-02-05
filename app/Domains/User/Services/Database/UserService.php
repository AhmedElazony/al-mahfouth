<?php

namespace App\Domains\User\Services\Database;

use App\Domains\Tahfidh\Models\StudentTajweed;
use App\Domains\User\Enums\UserGendersEnum;
use App\Domains\User\Enums\UserRolesEnum;
use App\Domains\User\Models\User;
use App\Domains\User\Services\Contracts\UserService as UserServiceContract;
use App\Http\Api\V1\Resources\User\UserResource;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceContract
{
    public function get(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator
    {
        return User::filter($filters)
            ->latest()
            ->paginate($perPage, $columns);
    }

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

            $this->createOrUpdateRoleProfile(
                $user,
                $user->role ?? $data['role'],
                $data
            );

            return $user;
        });

    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'username' => $data['username'] ?? $user->username,
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? $user->phone,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
            ]);

            $this->createOrUpdateRoleProfile(
                $user,
                $user->role,
                $data
            );

            return $user;
        });
    }

    public function delete(User $user): void
    {
        $user->delete();
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

    private function createOrUpdateRoleProfile(User $user, string $role, array $data)
    {
        return match ($role) {
            UserRolesEnum::TEACHER->value => $this->handleTeacherProfile($user, $data),
            UserRolesEnum::STUDENT->value => $this->handleStudentProfile($user, $data),
            default => null,
        };
    }

    private function handleTeacherProfile(User $user, array $data)
    {
        $currentProfile = $user->teacher;
        $user->teacher()->updateOrCreate(['user_id' => $user->id], [
            'created_by' => $currentProfile->created_by ?? auth()->id(),
            'specialization' => $data['teacher_specialization'] ?? $currentProfile->specialization ?? null,
        ]);
    }

    private function handleStudentProfile(User $user, array $data)
    {
        $currentProfile = $user->student;
        $user->student()->updateOrCreate(['user_id' => $user->id], [
            'created_by' => $currentProfile->created_by ?? auth()->id(),
            'educational_stage' => $data['student_educational_stage'] ?? $currentProfile->educational_stage ?? null,
            'begin_memorizing_at' => $data['student_begin_memorizing_at'] ?? $currentProfile->begin_memorizing_at ?? null,
            'memorizing_completed_at' => $data['student_memorizing_completed_at'] ?? $currentProfile->memorizing_completed_at ?? null,
        ]);

        if (isset($data['student_tajweed_recitation_level']) ||
            isset($data['student_tajweed_learning_status']) ||
            isset($data['student_tajweed_notes'])) {
            $tajweed = StudentTajweed::firstOrNew(['student_id' => $user->id]);
            $tajweed->recitation_level = $data['student_tajweed_recitation_level'] ?? $tajweed->recitation_level;
            $tajweed->learning_status = $data['student_tajweed_learning_status'] ?? $tajweed->learning_status;
            $tajweed->notes = $data['student_tajweed_notes'] ?? $tajweed->notes;
            $tajweed->save();
        }
    }
}
