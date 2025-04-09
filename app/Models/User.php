<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agency_id',
        'user_type',
        'phone',
        'address',
        'city',
        'country',
        'avatar',
        'is_active',
        'id_number',
        'passport_number',
        'nationality',
        'preferred_currency',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'notification_preferences' => 'array',
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
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the requests for the customer.
     */
    public function requests()
    {
        if (Schema::hasTable('service_requests')) {
            return $this->hasMany(Request::class, 'customer_id')->getQuery()->from('service_requests');
        }
        
        return $this->hasMany(Request::class, 'customer_id');
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
