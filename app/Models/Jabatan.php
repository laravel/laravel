<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class Jabatan extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'jabatan';
    
    protected $fillable = [
        'kode',
        'nama',
        'eselon',
        'tunjangan',
        'keterangan',
    ];

    protected $casts = [
        'tunjangan' => 'decimal:2',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }
}
