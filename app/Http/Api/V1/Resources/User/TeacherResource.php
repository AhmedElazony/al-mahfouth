<?php

namespace App\Http\Api\V1\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'name' => $this->whenLoaded('user',
                fn () => $this->user->name
            ),
            'specialization' => $this->specialization,
            'created_by' => $this->whenLoaded('createdBy',
                fn () => [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                ]),
        ];
    }
}
