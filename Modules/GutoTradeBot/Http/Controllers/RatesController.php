<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\GutoTradeBot\Entities\Rates;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;

class RatesController extends JsonsController
{
    public function create($date, $base, $coin, $rate)
    {
        $rate = Rates::create([
            'date' => $date,
            'base' => $base,
            'coin' => $coin,
            'rate' => $rate,
        ]);
        return $rate;
    }

}
