<?php

namespace App\Http\Api\V1\Requests\User;

use App\Support\Traits\HasPhoneValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    use HasPhoneValidation;

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
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'phone:EG'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
