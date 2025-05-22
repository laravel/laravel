<?php

namespace Modules\GutoTradeBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class Jsons extends Model
{
    use ModuleTrait;

    protected $casts = [
        'data' => 'json',
    ];
}
