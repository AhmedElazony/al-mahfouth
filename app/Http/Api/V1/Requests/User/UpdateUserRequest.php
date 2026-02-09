<?php

namespace App\Http\Api\V1\Requests\User;

use App\Domains\Tahfidh\Enums\EducationalStagesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\LearningStatusesEnum;
use App\Domains\User\Enums\UserGendersEnum;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->user->id)],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'phone' => ['nullable', 'string', 'phone:EG'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
			'gender' => ['sometimes', 'in:'.implode(',', UserGendersEnum::values())],
            'teacher_specialization' => ['sometimes', 'string', 'max:255'],
            'student_educational_stage' => ['sometimes', 'in:'.implode(',', EducationalStagesEnum::values())],
            'student_begin_memorizing_at' => ['sometimes', 'date', 'before_or_equal:today'],
            'student_memorizing_completed_at' => ['sometimes', 'date', 'after:student_begin_memorizing_at', 'before_or_equal:today'],
            'student_tajweed_recitation_level' => ['sometimes', 'in:'.implode(',', GradesEnum::values())],
            'student_tajweed_learning_status' => ['sometimes', 'in:'.implode(',', LearningStatusesEnum::values())],
            'student_tajweed_notes' => ['sometimes', 'string', 'max:1000'],
        ];
    }

    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUniquePhone($validator);
        });
    }

    protected function validateUniquePhone($validator): void
    {
        if (empty($this->phone)) {
            return;
        }

        $phone = phone($this->phone, 'EG')->formatE164();

        $exists = User::where('phone', $phone)
            ->where('id', '!=', $this->user->id)
            ->exists();

        if ($exists) {
            $validator->errors()->add('phone', __('validation.unique', ['attribute' => __('validation.attributes.phone')]));
        }
    }
}
