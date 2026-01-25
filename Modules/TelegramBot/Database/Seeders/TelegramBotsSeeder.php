<?php
namespace Modules\TelegramBot\Database\Seeders;


use Illuminate\Database\Seeder;
use Modules\TelegramBot\Entities\TelegramBots;
use App\Traits\ModuleTrait;

class TelegramBotsSeeder extends Seeder
{
    use ModuleTrait;

    public function run()
    {
        TelegramBots::create([
            'name' => '@ZentroNotificationBot',
            'token' => '8198488135:AAHjSshi4P3jTy_bPDDNZF2aIzSL0DkGxBg',
            'data' => [],
        ]);

        TelegramBots::create([
            'name' => '@GutoTradeBot',
            'token' => '7252174930:AAFJwAZaLrWiP-ONZHQZ7D0ps77HDoMkixQ',
            'data' => [],
        ]);
        TelegramBots::create([
            'name' => '@GutoTradeTestBot',
            'token' => '7543090584:AAEisZYB1NL24Wwwv2xQ2rVChOugyXYLdBU',
            'data' => [],
        ]);
        TelegramBots::create([
            'name' => '@IrelandPaymentsBot',
            'token' => '7286991852:AAG7TSW_hqF1bb-t7KU7toGVFx4SllCEDcM',
            'data' => [],
        ]);

        TelegramBots::create([
            'name' => '@ZentroTraderBot',
            'token' => '6989103595:AAH-qQww_v01UnAt9Ex0ZfmVp3qAIR9KXrE',
            'data' => [],
        ]);
        TelegramBots::create([
            'name' => '@ZentroBaseTelegramBot',
            'token' => '6055381762:AAEGjtR7MHpG7GmDIMVlKzxYzBFCBkobots',
            'data' => [],
        ]);
        TelegramBots::create([
            'name' => '@ZentroCriptoBot',
            'token' => '5797151131:AAF0o1P3C9wK8zx3OczGej9QmkILZmekJKc',
            'data' => [],
        ]);
        TelegramBots::create([
            'name' => '@ZentroLicensorBot',
            'token' => '1450849635:AAHpvMRi6EMdCajw6yZ9G6uma0WV1FF2JCY',
            'data' => [],
        ]);
        TelegramBots::create([
            'name' => '@ZentroOwnerBot',
            'token' => '7948651884:AAGI3FjcxYyaRkmuqrLsAZP34vQxz5B2LwA',
            'data' => [],
        ]);
        TelegramBots::create([
            'name' => '@ZentroPackageBot',
            'token' => '7948651884:AAGI3FjcxYyaRkmuqrLsAZP34vQxz5B2LwA',
            'data' => [],
        ]);
    }
}
