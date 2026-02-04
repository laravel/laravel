<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    // Relasi ke Materials (untuk guru)
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    // Relasi ke Reports (untuk guru/siswa)
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // Relasi ke Chat Messages (pengirim)
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Relasi ke Chat Messages (penerima)
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Helper untuk cek role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'guru';
    }

    public function isStudent()
    {
        return $this->role === 'siswa';
    }
}
