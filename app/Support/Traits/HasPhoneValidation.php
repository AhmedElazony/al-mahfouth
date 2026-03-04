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
            'phone' => $this->formatPhone($this->phone),
        ]);
    }

    protected function formatPhone(string $phone, $country = 'EG'): string
    {
        return rescue(function () use ($phone, $country) {
            return str_replace(' ', '', phone($phone, $country)->formatE164());
        }, $phone);
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

        $phone = $this->formatPhone($this->phone);

        $exists = User::where('phone', $phone)->exists();

        if ($exists) {
            $validator->errors()->add('phone', __('validation.unique', ['attribute' => __('validation.attributes.phone')]));
        }
    }
}
