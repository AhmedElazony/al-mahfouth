<?php

namespace App\Http\Api\V1\Resources\User;

use App\Domains\Tahfidh\Enums\EducationalStagesEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'educational_stage' => [
                'for_view' => EducationalStagesEnum::from($this->educational_stage)->label(),
                'value' => $this->educational_stage,
            ],
            'begin_memorizing_at' => $this->begin_memorizing_at?->toDateString(),
            'memorizing_completed_at' => $this->memorizing_completed_at?->toDateString(),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
        ];
    }
}
