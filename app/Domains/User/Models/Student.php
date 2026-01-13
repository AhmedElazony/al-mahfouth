<?php

namespace App\Domains\User\Models;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Models\GroupStudent;
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

    protected $with = ['user:id,name'];

    protected $casts = [
        'begin_memorizing_at' => 'date',
        'memorizing_completed_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'user_id';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function groups()
    {
        return $this->belongsToMany(
            Group::class,
            'group_student',
            'student_id',
            'group_id',
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
