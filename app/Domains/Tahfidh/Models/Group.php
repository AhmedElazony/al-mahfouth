<?php

namespace App\Domains\Tahfidh\Models;

use App\Domains\User\Models\Student;
use App\Domains\User\Models\Teacher;
use App\Domains\User\Traits\HasFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory, HasFilters;

    protected $fillable = [
        'name',
        'teacher_id',
        'schedule',
        'is_online',
        'is_active',
    ];

    protected $casts = [
        'schedule' => 'array',
        'is_online' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id');
    }

    public function groupStudents()
    {
        return $this->hasMany(GroupStudent::class, 'group_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'group_student',
            'group_id',
            'student_id',
            'id',
            'user_id'
        )->using(GroupStudent::class)
            ->withPivot([
                'student_status',
                'memorizing_amount',
                'joined_at',
            ])->withTimestamps();
    }
}
