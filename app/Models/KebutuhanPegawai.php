<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedRouteKey;

class KebutuhanPegawai extends Model
{
    use HasEncryptedRouteKey;
    
    protected $table = 'kebutuhan_pegawai';
    
    protected $fillable = [
        'unit_kerja_id',
        'jabatan_id',
        'jumlah_kebutuhan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_kebutuhan' => 'integer',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    /**
     * Get bezetting (jumlah pegawai eksisting) untuk jabatan ini di unit kerja ini
     */
    public function getBezettingAttribute()
    {
        return Pegawai::where('unit_kerja_id', $this->unit_kerja_id)
            ->where('jabatan_id', $this->jabatan_id)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get selisih antara kebutuhan dan bezetting
     * Positif = kelebihan pegawai, Negatif = kekurangan pegawai
     */
    public function getSelisihAttribute()
    {
        return $this->bezetting - $this->jumlah_kebutuhan;
    }

    /**
     * Get status kebutuhan pegawai
     */
    public function getStatusAttribute()
    {
        $selisih = $this->selisih;
        
        if ($selisih == 0) {
            return 'Terpenuhi';
        } elseif ($selisih > 0) {
            return 'Kelebihan';
        } else {
            return 'Kekurangan';
        }
    }

    /**
     * Get status class untuk badge
     */
    public function getStatusClassAttribute()
    {
        $selisih = $this->selisih;
        
        if ($selisih == 0) {
            return 'success';
        } elseif ($selisih > 0) {
            return 'warning';
        } else {
            return 'danger';
        }
    }
}
