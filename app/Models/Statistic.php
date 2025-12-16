<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'groups_count',
        'students_count',
        'teachers_count',
        'active_students_count',
        'inactive_students_count',
    ];

    protected $casts = [

    ];
}
