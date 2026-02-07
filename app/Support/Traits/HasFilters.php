<?php

namespace App\Support\Traits;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Models\Report;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    #[Scope()]
    protected function filter(Builder $query, array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $field => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($this instanceof User) {
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
            } elseif ($this instanceof Group) {
                if ($field === 'q') {
                    $query->where('name', 'LIKE', "%$value%");
                }

                if ($field === 'teacher_id') {
                    $query->where('teacher_id', $value);
                }

                if ($field === 'is_active') {
                    $query->where('is_active', filter_var($value, FILTER_VALIDATE_BOOLEAN));
                }
            } elseif ($this instanceof Report) {
                if ($field === 'student_id') {
                    $query->where('student_id', $value);
                }

                if ($field === 'group_id') {
                    $query->where('group_id', $value);
                }

                if ($field === 'date_from') {
                    $query->whereDate('date', '>=', $value);
                }

                if ($field === 'date_to') {
                    $query->whereDate('date', '<=', $value);
                }
            }
        }

        return $query;
    }
}
