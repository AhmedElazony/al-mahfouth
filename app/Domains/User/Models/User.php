<?php

namespace App\Domains\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\User\Enums\UserRolesEnum;
use App\Domains\User\Traits\HasFilters;
use App\Domains\User\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasFilters, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified_at',
        'role',
        'password',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone' => E164PhoneNumberCast::class.':EG',
        ];
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // Helper Methods
    public function loadRoleRelations()
    {
        if ($this->hasAdminRole()) {
            return $this;
        }

        $relations = match ($this->role) {
            UserRolesEnum::STUDENT->value => ['student', 'student.tajweed'],
            UserRolesEnum::TEACHER->value => ['teacher'],
            default => [],
        };

        return $this->load($relations);
    }
}
