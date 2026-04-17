<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class Kgb extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'kgb';
    
    protected $fillable = [
        'nomor_sk',
        'pegawai_id',
        'tmt_kgb',
        'tmt_kgb_berikutnya',
        'golongan_lama_id',
        'golongan_baru_id',
        'gaji_pokok_lama',
        'gaji_pokok_baru',
        'masa_kerja_tahun',
        'masa_kerja_bulan',
        'status',
        'keterangan',
        'disetujui_oleh',
        'tanggal_disetujui',
    ];

    protected $casts = [
        'tmt_kgb' => 'date',
        'tmt_kgb_berikutnya' => 'date',
        'tanggal_disetujui' => 'datetime',
        'gaji_pokok_lama' => 'decimal:2',
        'gaji_pokok_baru' => 'decimal:2',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function golonganLama()
    {
        return $this->belongsTo(Golongan::class, 'golongan_lama_id');
    }

    public function golonganBaru()
    {
        return $this->belongsTo(Golongan::class, 'golongan_baru_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public static function generateNomorSK()
    {
        $tahun = date('Y');
        $count = self::whereYear('created_at', $tahun)->count() + 1;
        return sprintf('%03d/KGB/%s', $count, $tahun);
    }
}
