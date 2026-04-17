<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoCuti extends Model
{
    protected $table = 'saldo_cuti';
    
    protected $fillable = [
        'pegawai_id',
        'jenis_cuti_id',
        'tahun',
        'saldo_awal',
        'saldo_terpakai',
        'saldo_sisa',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class);
    }
}
