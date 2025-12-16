<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'students_count',
        'commited_count',
        'uncommited_count',
        'absent_count',
        'inactive_count',
    ];

    protected $casts = [

    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
