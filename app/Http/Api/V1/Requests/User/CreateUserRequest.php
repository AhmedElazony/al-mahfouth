<?php

namespace App\Http\Api\V1\Requests\User;

use App\Domains\Tahfidh\Enums\EducationalStagesEnum;
use App\Domains\User\Enums\UserRolesEnum;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'phone:EG'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => [
                'required',
                'in:'.implode(',', Arr::except(UserRolesEnum::values(), [UserRolesEnum::SUPER_ADMIN->value])),
            ],
            'teacher_specialization' => ['required_if:role,'.UserRolesEnum::TEACHER->value, 'string', 'max:255'],
            'student_educational_stage' => ['required_if:role,'.UserRolesEnum::STUDENT->value, 'in:'.implode(',', EducationalStagesEnum::values())],
            'student_begin_memorizing_at' => ['nullable', 'date', 'before_or_equal:today'],
            'student_memorizing_completed_at' => ['nullable', 'date', 'after_or_equal:student_begin_memorizing_at'],
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
        $phone = phone($this->phone, 'EG')->formatE164();

        if (empty($phone)) {
            return;
        }

        $exists = User::where('phone', $phone)->exists();

        if ($exists) {
            $validator->errors()->add('phone', __('validation.unique', ['attribute' => __('validation.attributes.phone')]));
        }
    }
}
