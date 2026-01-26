<?php

namespace Modules\ZentroPackageBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class Histories extends Model
{
    use ModuleTrait;

    protected $table = 'histories';
    protected $guarded = [];

    /*
    protected $fillable = [
        'package_id', 'status', 'location', 'comment', 'user_id'
    ];
    */

    public function package()
    {
        return $this->belongsTo(Packages::class);
    }

    /**
     * El usuario (mensajero) que realizó el escaneo o cambio.
     */
    public function user()
    {
        return $this->belongsTo(Packages::class);
    }
}
