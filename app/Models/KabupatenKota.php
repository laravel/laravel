<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class KabupatenKota extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'kabupaten_kota';
    
    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'provinsi',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function getNamaLengkapAttribute()
    {
        return $this->tipe . ' ' . $this->nama;
    }
}
