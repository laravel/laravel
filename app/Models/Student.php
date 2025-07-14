<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Institution;
use App\Models\Department;
use App\Models\TestAttempt;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'institute_id',
        'department_id',
        'registration_date',
        'last_login',
        'test_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function institute()
    {
        return $this->belongsTo(Institution::class, 'institute_id');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }
    public function scopeSearchBy($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orWhere('phone', 'LIKE', "%{$search}%");
    }   
}
