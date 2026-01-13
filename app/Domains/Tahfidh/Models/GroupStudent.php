<?php

namespace App\Domains\Tahfidh\Models;

use App\Domains\User\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupStudent extends Pivot
{
    use HasFactory;

    protected $table = 'group_student';

    protected $fillable = [
        'group_id',
        'student_id',
        'student_status',
        'memorizing_amount',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
