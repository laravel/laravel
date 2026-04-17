<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasEncryptedRouteKey;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasEncryptedRouteKey;

    protected $fillable = [
        'username',
        'nama',
        'password',
        'role',
        'pegawai_id',
        'unit_kerja_id',
        'is_active',
        'must_change_password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSubAdmin()
    {
        return $this->role === 'sub_admin';
    }

    public function isPegawai()
    {
        return $this->role === 'pegawai';
    }

    /**
     * Check if user has admin-level access (admin or sub_admin)
     */
    public function hasAdminAccess()
    {
        return in_array($this->role, ['admin', 'sub_admin']);
    }

    /**
     * Check if user can access a specific unit kerja
     */
    public function canAccessUnitKerja($unitKerjaId)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        if ($this->isSubAdmin()) {
            return $this->unit_kerja_id == $unitKerjaId;
        }
        
        return false;
    }

    /**
     * Check if user can access a specific pegawai
     */
    public function canAccessPegawai(Pegawai $pegawai)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        if ($this->isSubAdmin()) {
            return $this->unit_kerja_id == $pegawai->unit_kerja_id;
        }
        
        // Pegawai can only access their own data
        if ($this->isPegawai()) {
            return $this->pegawai_id == $pegawai->id;
        }
        
        return false;
    }
}
