<?php

namespace App\Http\Api\V1\Requests\Tahfidh\Groups;

use App\Domains\Tahfidh\Enums\DaysEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'teacher_id' => ['sometimes', 'exists:teachers,user_id'],
            'is_online' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'schedule' => ['sometimes', 'array', 'min:1', 'max:7'],
            'schedule.*.day' => ['sometimes', 'in:'.implode(',', DaysEnum::values())],
            'schedule.*.start_time' => ['sometimes', 'date_format:H:i'],
            'schedule.*.end_time' => ['sometimes', 'date_format:H:i', 'after:schedule.*.start_time'],
        ];
    }
}
