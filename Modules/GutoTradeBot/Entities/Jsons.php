<?php

namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Eloquent\Model;

class Jsons extends Model
{
    use UsesModuleConnection;

    protected $casts = [
        'data' => 'json',
    ];
}
