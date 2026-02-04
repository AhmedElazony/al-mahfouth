<?php

namespace App\Http\Api\V1\Resources\User;

use App\Domains\Tahfidh\Enums\EducationalStagesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\LearningStatusesEnum;
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
            'name' => $this->whenLoaded('user', fn () => $this->user->name),
            'educational_stage' => [
                'for_view' => EducationalStagesEnum::from($this->educational_stage)->label(),
                'value' => $this->educational_stage,
            ],
            'begin_memorizing_at' => $this->begin_memorizing_at?->toDateString(),
            'memorizing_completed_at' => $this->memorizing_completed_at?->toDateString(),
            'tajweed' => $this->whenLoaded('tajweed', fn () => [
                'recitation_level' => [
                    'for_view' => GradesEnum::from($this->tajweed->recitation_level)->label(),
                    'value' => $this->tajweed->recitation_level,
                ],
                'learning_status' => [
                    'for_view' => LearningStatusesEnum::from($this->tajweed->learning_status)->label(),
                    'value' => $this->tajweed->learning_status,
                ],
                'notes' => $this->tajweed->notes,
            ]),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
        ];
    }
}
