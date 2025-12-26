<?php

namespace App\Http\Api\V1\Resources\User;

use App\Domains\User\Enums\UserGendersEnum;
use App\Domains\User\Enums\UserRolesEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
            'phone' => $this->phone,
            'phone_verified' => $this->phone_verified_at !== null,
            'gender' => [
                'for_view' => UserGendersEnum::from($this->gender)->label(),
                'value' => $this->gender,
            ],
            'role' => [
                'for_view' => UserRolesEnum::from($this->role)->label(),
                'value' => $this->role,
            ],
            'profile' => match ($this->role) {
                UserRolesEnum::TEACHER->value => new TeacherResource($this->whenLoaded('teacher')),
                UserRolesEnum::STUDENT->value => new StudentResource($this->whenLoaded('student')),
                default => null,
            },
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
