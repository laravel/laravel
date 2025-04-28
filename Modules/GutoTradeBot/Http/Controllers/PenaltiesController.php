<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\GutoTradeBot\Entities\Penalties;

class PenaltiesController extends JsonsController
{

    public function getAll()
    {

        return Penalties::where("id", ">", 0)->get();
    }

    public function getForAmount($amount)
    {
        return Penalties::where("to", ">=", $amount)
            ->where("from", "<=", $amount)->first();
    }
}
