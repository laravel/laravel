<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'phone', 'organization_name', 'designation', 'user_id', 'password', 'whatsapp_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        
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
        ];
    }
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }
    public function abstracts()
    {
        return $this->hasMany(AbstractUpload::class);
    }
    /**
     * Get the abstract uploads associated with the user.
     */
    public function abstractUploads()
    {
        return $this->hasMany(AbstractUpload::class);
    }
}
