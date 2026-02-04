<?php

namespace App\Http\Api\V1\Resources\User;

use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\LearningStatusesEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentTajweedResource extends JsonResource
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
            'recitation_level' => [
                    'for_view' => GradesEnum::from($this->recitation_level)->label(),
                    'value' => $this->recitation_level,
                ],
                'learning_status' => [
                    'for_view' => LearningStatusesEnum::from($this->learning_status)->label(),
                    'value' => $this->learning_status,
                ],
                'notes' => $this->notes,
        ];
    }
}
