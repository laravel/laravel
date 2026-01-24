<?php

namespace Modules\TelegramBot\Database\Seeders;


use Illuminate\Database\Seeder;
use Modules\TelegramBot\Entities\Actors;
use Modules\ZentroTraderBot\Entities\Suscriptions;
use App\Traits\ModuleTrait;

class ActorsSeeder extends Seeder
{
    use ModuleTrait;

    public function run()
    {
        foreach (Actors::$KNOWN_ACTORS as $username => $actor) {
            Actors::create([
                'user_id' => $actor["user_id"],
                'data' => array(
                    "GutoTradeBot" => Actors::getTemplate($actor["role"]),
                    "GutoTradeTestBot" => Actors::getTemplate($actor["role"]),
                    "IrelandPaymentsBot" => Actors::getTemplate($actor["role"]),
                ),
            ]);
        }
    }
}
