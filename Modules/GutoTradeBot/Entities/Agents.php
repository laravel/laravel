<?php

namespace Modules\GutoTradeBot\Entities;

use Modules\GutoTradeBot\Http\Controllers\PaymentsController;
use Modules\TelegramBot\Entities\Actors;

class Agents extends Actors
{

    public function unconfirmedMoneys($bot, $pc)
    {
        $amount = (float) ($pc->getUnconfirmedQuery($bot, Payments::class, $this->user_id)->sum('amount'));
        return $amount;
    }

    public function liquidatedMoneys($bot, $pc)
    {
        $amount = (float) ($pc->getUnliquidatedQuery($bot, Payments::class, $this->user_id)->sum('amount'));
        return $amount;
    }

}
