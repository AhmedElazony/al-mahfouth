<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'educational_stage',
        'code',
        'begin_memorizing_at',
        'memorizing_completed_at',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'begin_memorizing_at' => 'date',
        'memorizing_completed_at' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
