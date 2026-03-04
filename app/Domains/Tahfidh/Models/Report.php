<?php

namespace App\Domains\Tahfidh\Models;

use App\Domains\User\Models\Student;
use App\Domains\User\Models\User;
use App\Support\Traits\HasFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory, HasFilters;

    protected $fillable = [
        'student_id',
        'group_id',
        'created_by',
        'date',
        'attendance_status',
        'memorized_amount',
        'grade',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
