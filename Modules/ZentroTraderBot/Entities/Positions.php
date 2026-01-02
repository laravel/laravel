<?php

namespace Modules\ZentroTraderBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class Positions extends Model
{
    use ModuleTrait;

    protected $table = 'positions';
    protected $guarded = [];
}
