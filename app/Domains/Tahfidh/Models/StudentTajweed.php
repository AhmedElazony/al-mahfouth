<?php

namespace App\Domains\Tahfidh\Models;

use App\Domains\User\Models\Student;
use Illuminate\Database\Eloquent\Model;

class StudentTajweed extends Model
{
    protected $table = 'student_tajweed';

    protected $fillable = [
        'student_id',
        'recitation_level',
        'learning_status',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }
}
