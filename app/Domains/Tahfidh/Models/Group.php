<?php

namespace App\Domains\Tahfidh\Models;

use App\Domains\User\Models\Student;
use App\Domains\User\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'teacher_id',
        'schedule',
        'is_active',
    ];

    protected $casts = [
        'schedule' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
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
            'student_id'
        )->using(GroupStudent::class)
            ->withPivot([
                'group_id',
                'student_id',
                'student_status',
                'is_online',
                'memorizing_amount',
                'joined_at',
                'left_at',
            ])->withTimestamps();
    }
}
