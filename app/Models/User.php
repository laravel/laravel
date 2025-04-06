<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'user_type', 'agency_id', 'parent_id', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_subagent');
    }

    public function customerRequests()
    {
        return $this->hasMany(Request::class, 'customer_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'subagent_id');
    }

    /**
     * تحديد ما إذا كان المستخدم وكيلاً
     */
    public function isAgency()
    {
        return $this->user_type === 'agency';
    }

    /**
     * تحديد ما إذا كان المستخدم سبوكيلاً
     */
    public function isSubagent()
    {
        return $this->user_type === 'subagent';
    }

    /**
     * تحديد ما إذا كان المستخدم عميلاً
     */
    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }
}
