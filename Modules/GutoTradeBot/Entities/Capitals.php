<?php

namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;
use Modules\GutoTradeBot\Http\Controllers\ProfitsController;

class Capitals extends Moneys
{
    use UsesModuleConnection;

    protected $table = "capitals";
}
