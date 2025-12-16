<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupStudent extends Model
{
    use HasFactory;

    protected $table = 'group_student';

    protected $fillable = [
        'group_id',
        'student_id',
        'student_status',
        'is_online',
        'memorizing_amount',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
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
