<?php

namespace App\Domains\User\Models;

use App\Domains\Tahfidh\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

	protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'specialization',
        'created_by',
    ];

    protected $with = ['user:id,name'];

    protected $casts = [
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

    public function groups(): HasMany
    {
        return $this->hasMany(
            Group::class,
            'teacher_id',
            'user_id'
        );
    }
}
