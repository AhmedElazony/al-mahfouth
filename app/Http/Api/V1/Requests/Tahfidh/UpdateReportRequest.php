<?php

namespace App\Http\Api\V1\Requests\Tahfidh;

use App\Domains\Tahfidh\Enums\AttendanceStatusesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\MemorizingAmountsEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
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
			'student_id' => ['sometimes', 'integer'],
            'date' => ['sometimes', 'date', 'date_format:d-m-Y'],
            'attendance_status' => ['sometimes', 'in:'.implode(',', AttendanceStatusesEnum::values())],
            'memorized_amount' => ['sometimes', 'in:'.implode(',', MemorizingAmountsEnum::values())],
            'grade' => ['sometimes', 'in:'.implode(',', GradesEnum::values())],
            'notes' => ['sometimes', 'string', 'min:2', 'max:1024'],
        ];
    }
}
