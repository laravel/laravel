<?php

namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;
use Modules\GutoTradeBot\Http\Controllers\ProfitsController;

class Payments extends Moneys
{
    use UsesModuleConnection;

    protected $table = "payments";
}
