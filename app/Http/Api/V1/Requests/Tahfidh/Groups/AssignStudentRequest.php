<?php

namespace App\Http\Api\V1\Requests\Tahfidh\Groups;

use App\Domains\Tahfidh\Enums\MemorizingAmountsEnum;
use App\Domains\Tahfidh\Enums\StudentStatusesEnum;
use Illuminate\Foundation\Http\FormRequest;

class AssignStudentRequest extends FormRequest
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
            'student_id' => ['required', 'exists:students,user_id'],
            'student_status' => ['nullable', 'in:'.implode(',', StudentStatusesEnum::values())],
            'memorizing_amount' => ['required', 'in:'.implode(',', MemorizingAmountsEnum::values())],
        ];
    }
}
