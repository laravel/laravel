<?php

namespace Modules\TelegramBot\Database\Seeders;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Seeder;
use Modules\TelegramBot\Entities\Actors;
use Modules\ZentroTraderBot\Entities\TradingSuscriptions;

class ActorsSeeder extends Seeder
{
    use UsesModuleConnection;

    public function run()
    {
        foreach (Actors::$KNOWN_ACTORS as $username => $actor) {
            Actors::create([
                'user_id' => $actor["user_id"],
                'data' => array(
                    "GutoTradeBot" => Actors::getTemplate($actor["role"]),
                    "GutoTradeTestBot" => Actors::getTemplate($actor["role"]),
                    "IrelandPaymentsBot" => Actors::getTemplate($actor["role"]),
                    "ZentroTraderBot" => isset(TradingSuscriptions::$KNOWN_SUSCRIPTORS[$username]) ? TradingSuscriptions::getSuscriptorTemplate(TradingSuscriptions::$KNOWN_SUSCRIPTORS[$username]) : TradingSuscriptions::getSuscriptorTemplate(),
                ),
            ]);
        }
    }
}
