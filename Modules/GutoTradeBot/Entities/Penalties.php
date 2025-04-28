<?php

namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Eloquent\Model;

class Penalties extends Model
{
    use UsesModuleConnection;

    protected $fillable = ['from', 'to', 'amount'];

    public $timestamps = false;

}
