<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class JenisCuti extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'jenis_cuti';
    
    protected $fillable = [
        'nama',
        'max_hari',
        'keterangan',
    ];

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function saldoCuti()
    {
        return $this->hasMany(SaldoCuti::class);
    }
}
