<?php

namespace Modules\GutoTradeBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class Penalties extends Model
{
    use ModuleTrait;
    protected $fillable = ['from', 'to', 'amount'];

    public $timestamps = false;

}
