<?php

namespace App\Http\Api\V1\Resources\User;

use App\Http\Api\V1\Resources\Tahfidh\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group' => GroupResource::make($this),
            'student_status' => $this->pivot?->student_status,
            'memorizing_amount' => $this->pivot?->memorizing_amount,
            'joined_at' => $this->pivot?->joined_at->toDateTimeString(),
        ];
    }
}
