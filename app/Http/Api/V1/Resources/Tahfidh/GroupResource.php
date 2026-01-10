<?php

namespace App\Http\Api\V1\Resources\Tahfidh;

use App\Http\Api\V1\Resources\User\TeacherResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'teacher' => $this->whenLoaded('teacher',
                fn () => TeacherResource::make($this->teacher)
            ),
            'schedule' => $this->schedule,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
