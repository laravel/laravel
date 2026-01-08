<?php
namespace Modules\ZentroTraderBot\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Metadatas;

class MetadataSeeder extends Seeder
{

    public function run(): void
    {
        //  Seeders to this Module
        // -------------------------------------------------------------------------------

        Metadatas::create([
            'name' => 'app_zentrotraderbot_telegram_notifications_channel',
            'value' => '-1001994576446',
            'comment' => 'Canal de Telegram a donde enviar notificaciones de operaciones de la comunidad del bot de Trading',
            'metadatatype' => 1,
            'is_visible' => 1,
        ]);
        Metadatas::create([
            'name' => 'app_zentrotraderbot_tradingview_alert_action_level',
            'value' => '2',
            'comment' => 'Acciones a realizar al recibir alerta desde TradingView [1: alertar en canal, 2: alertar y ejecutar ordenes en CEX]',
            'metadatatype' => 1,
            'is_visible' => 1,
        ]);
    }
}
