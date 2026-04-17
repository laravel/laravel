<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasEncryptedRouteKey;

class Pegawai extends Model
{
    use HasEncryptedRouteKey;

    private const NON_ACTIVE_EMPLOYMENT_STATUS = ['Berhenti/Keluar', 'Pensiun'];
    
    protected $table = 'pegawai';
    
    protected $fillable = [
        'nip',
        'nik',
        'npwp',
        'no_rekening',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'alamat',
        'kabupaten_kota_id',
        'telepon',
        'email',
        'status_perkawinan',
        'pendidikan_terakhir',
        'jurusan_pendidikan',
        'golongan_id',
        'jabatan_id',
        'unit_kerja_id',
        'tmt_cpns',
        'tmt_pns',
        'tmt_golongan',
        'tmt_jabatan',
        'status_pegawai',
        'foto',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
        'tmt_golongan' => 'date',
        'tmt_jabatan' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $pegawai): void {
            if ($pegawai->mustBeInactiveByStatus()) {
                $pegawai->is_active = false;
            }
        });

        static::saved(function (self $pegawai): void {
            if (!$pegawai->user) {
                return;
            }

            if ($pegawai->user->is_active !== $pegawai->is_active) {
                $pegawai->user->update(['is_active' => $pegawai->is_active]);
            }
        });
    }

    public function mustBeInactiveByStatus(): bool
    {
        return in_array($this->status_pegawai, self::NON_ACTIVE_EMPLOYMENT_STATUS, true);
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function kabupatenKota()
    {
        return $this->belongsTo(KabupatenKota::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function perjalananDinas()
    {
        return $this->hasMany(PerjalananDinas::class);
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function kgb()
    {
        return $this->hasMany(Kgb::class);
    }

    public function saldoCuti()
    {
        return $this->hasMany(SaldoCuti::class);
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? Carbon::parse($this->tanggal_lahir)->age : null;
    }

    public function getMasaKerjaAttribute()
    {
        if (!$this->tmt_pns) return null;
        
        $tmt = Carbon::parse($this->tmt_pns);
        $now = Carbon::now();
        $diff = $tmt->diff($now);
        
        return $diff->y . ' Tahun ' . $diff->m . ' Bulan';
    }
}
