<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class PerjalananDinas extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'perjalanan_dinas';
    
    protected $fillable = [
        'nomor_surat',
        'pegawai_id',
        'tanggal_berangkat',
        'tanggal_kembali',
        'tujuan',
        'maksud_perjalanan',
        'jenis_transportasi',
        'biaya_transport',
        'biaya_penginapan',
        'uang_harian',
        'biaya_lainnya',
        'total_biaya',
        'status',
        'keterangan',
        'disetujui_oleh',
        'tanggal_disetujui',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_disetujui' => 'datetime',
        'biaya_transport' => 'decimal:2',
        'biaya_penginapan' => 'decimal:2',
        'uang_harian' => 'decimal:2',
        'biaya_lainnya' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function getLamaDinasAttribute()
    {
        return $this->tanggal_berangkat->diffInDays($this->tanggal_kembali) + 1;
    }

    public static function generateNomorSurat()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $count = self::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count() + 1;
        return sprintf('%03d/SPPD/%s/%s', $count, $bulan, $tahun);
    }
}
