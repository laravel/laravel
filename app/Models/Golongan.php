<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class Golongan extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'golongan';
    
    protected $fillable = [
        'kode',
        'nama',
        'gaji_pokok',
        'keterangan',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }
}
