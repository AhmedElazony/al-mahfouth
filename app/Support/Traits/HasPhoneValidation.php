<?php

namespace App\Support\Traits;

use App\Domains\User\Models\User;

trait HasPhoneValidation
{
    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUniquePhone($validator);
        });

		$validator->before(function ($validator) {
			if (empty($this->phone)) {
				return;
			}

			$phone = phone($this->phone, 'EG');

			if (!$phone->isValid()) {
				$validator->errors()->add('phone', __('validation.phone', ['attribute' => __('validation.attributes.phone')]));
			}
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
