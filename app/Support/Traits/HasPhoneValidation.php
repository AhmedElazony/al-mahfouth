<?php

namespace App\Support\Traits;

use App\Domains\User\Models\User;

trait HasPhoneValidation
{
	protected function prepareForValidation(): void
	{
		if (empty($this->phone)) {
			return;
		}

		$this->merge([
			'phone' => phone($this->phone, 'EG')->formatE164(),
		]);
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

        $exists = User::where('phone', $phone)->exists();

        if ($exists) {
            $validator->errors()->add('phone', __('validation.unique', ['attribute' => __('validation.attributes.phone')]));
        }
    }
}
