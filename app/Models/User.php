<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // Comentado hasta que se instale Sanctum

class User extends Authenticatable
{
    use HasFactory, Notifiable; // Versión temporal sin Sanctum

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

    public function isAgency()
    {
        return $this->user_type === 'agency';
    }

    public function isSubagent()
    {
        return $this->user_type === 'subagent';
    }

    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }
}
