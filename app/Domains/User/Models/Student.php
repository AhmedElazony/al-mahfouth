<?php

namespace App\Domains\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'educational_stage',
        'begin_memorizing_at',
        'memorizing_completed_at',
        'created_by',
    ];

    protected $casts = [
        'begin_memorizing_at' => 'date',
        'memorizing_completed_at' => 'date',
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
