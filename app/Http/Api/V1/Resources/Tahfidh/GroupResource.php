<?php

namespace App\Http\Api\V1\Resources\Tahfidh;

use App\Domains\Tahfidh\Enums\DaysEnum;
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
            'is_online' => $this->is_online,
            'schedule' => $this->formatSchedule($this->schedule),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

    private function formatSchedule(array $schedule): array
    {
        return array_map(function ($item) {
            return [
                'day' => [
                    'for_view' => DaysEnum::from($item['day'])->label(),
                    'value' => $item['day'],
                ],
                'start_time' => [
                    'for_view' => date('h:i a', strtotime($item['start_time'])),
                    'value' => $item['start_time'],
                ],
                'end_time' => [
                    'for_view' => date('h:i a', strtotime($item['end_time'])),
                    'value' => $item['end_time'],
                ],
            ];
        }, $schedule);
    }
}
