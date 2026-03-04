<?php

namespace App\Http\Api\V1\Resources\Tahfidh;

use App\Domains\Tahfidh\Enums\AttendanceStatusesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\MemorizingAmountsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student_id,
                'name' => $this->student->user->name,
            ]),
            'date' => $this->date->format('d-m-Y'),
            'attendance_status' => [
                'for_view' => AttendanceStatusesEnum::from($this->attendance_status)->label(),
                'value' => $this->attendance_status,
            ],
            'memorized_amount' => [
                'for_view' => isset($this->memorized_amount) ? MemorizingAmountsEnum::from($this->memorized_amount)->label() : null,
                'value' => $this->memorized_amount,
            ],
            'grade' => [
                'for_view' => isset($this->grade) ? GradesEnum::from($this->grade)->label() : null,
                'value' => $this->grade,
            ],
            'notes' => $this->notes,
            'group' => $this->whenLoaded('group', fn () => [
                'id' => $this->group_id,
                'name' => $this->group->name,
            ]),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'id' => $this->created_by,
                'name' => $this->createdBy->name,
            ]),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
