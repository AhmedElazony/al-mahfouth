<?php

namespace App\Domains\User\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    #[Scope()]
    protected function filter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($field === 'q') {
                $query->where('name', 'LIKE', "%$value%")
                    ->orWhere('email', 'LIKE', "%$value%")
                    ->orWhere('username', 'LIKE', "%$value%");
            }

            if ($field === 'role') {
                $query->where('role', $value);
            }

            if ($field === 'gender') {
                $query->where('gender', $value);
            }
        }

        return $query;
    }
}
