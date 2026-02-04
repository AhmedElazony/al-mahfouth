<?php

namespace App\Http\Api\V1\Requests\User;

use App\Domains\Tahfidh\Enums\EducationalStagesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\LearningStatusesEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentProfileRequest extends FormRequest
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
            'student_id' => ['required', 'integer'],
            'educational_stage' => ['sometimes', 'in:'.implode(',', EducationalStagesEnum::values())],
            'begin_memorizing_at' => ['sometimes', 'date', 'before_or_equal:today'],
            'memorizing_completed_at' => ['sometimes', 'date', 'after:student_begin_memorizing_at', 'before_or_equal:today'],
            'tajweed_recitation_level' => ['sometimes', 'in:'.implode(',', GradesEnum::values())],
            'tajweed_learning_status' => ['sometimes', 'in:'.implode(',', LearningStatusesEnum::values())],
            'tajweed_notes' => ['sometimes', 'string', 'max:1000'],
        ];
    }
}
