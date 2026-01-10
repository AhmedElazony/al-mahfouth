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
            'group' => $this->whenLoaded('group',
                fn () => GroupResource::make($this->group)
            ),
            'student' => $this->whenLoaded('student',
                fn () => [
                    'id' => $this->student->id,
                    'name' => $this->student->name,
                ]
            ),
            'student_status' => [
                'for_view' => StudentStatusesEnum::from($this->student_status)->label(),
                'value' => $this->student_status,
            ],
            'is_online' => $this->is_online,
            'memorizing_amount' => [
                'for_view' => MemorizingAmountsEnum::from($this->memorizing_amount)->label(),
                'value' => $this->memorizing_amount,
            ],
            'joined_at' => $this->joined_at?->toDateTimeString(),
        ];
    }
}
