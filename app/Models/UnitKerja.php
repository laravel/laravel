<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class UnitKerja extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'unit_kerja';
    
    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'telepon',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }
}
