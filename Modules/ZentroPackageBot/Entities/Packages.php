<?php

namespace Modules\ZentroPackageBot\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ModuleTrait;

class Packages extends Model
{
    use ModuleTrait;
    use SoftDeletes;

    protected $table = 'packages';
    protected $guarded = [];

    /*
        protected $fillable = [
            'tracking_number',
            'awb',
            'internal_ref',
            'recipient_name',
            'recipient_id',
            'recipient_phone',
            'full_address',
            'destination_code',
            'province',
            'description',
            'weight_kg',
            'type',
            'pieces',
            'sender_name',
            'sender_email',
            'status'
        ];
        */

    /**
     * Obtiene todos los movimientos del paquete.
     */
    public function history()
    {
        return $this->hasMany(Histories::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene el último estado registrado.
     */
    public function lastHistory()
    {
        return $this->hasOne(Histories::class)->latestOfMany();
    }
}
