<?php

namespace App\Http\Api\V1\Resources\Tahfidh;

use App\Domains\Tahfidh\Enums\MemorizingAmountsEnum;
use App\Domains\Tahfidh\Enums\StudentStatusesEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupStudentResource extends JsonResource
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
            'student' => $this->whenLoaded('user',
                fn () => [
                    'id' => $this->user_id,
                    'name' => $this->user->name,
                ]
            ),
            'student_status' => [
                'for_view' => isset($this->pivot?->student_status) ? StudentStatusesEnum::from($this->pivot?->student_status)->label() : null,
                'value' => $this->pivot?->student_status,
            ],
            'memorizing_amount' => [
                'for_view' => isset($this->pivot?->memorizing_amount) ? MemorizingAmountsEnum::from($this->pivot?->memorizing_amount)->label() : null,
                'value' => $this->pivot?->memorizing_amount,
            ],
            'joined_at' => $this->pivot?->joined_at?->toDateTimeString(),
        ];
    }
}
