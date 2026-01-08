<?php
namespace Database\Seeders;

use App\Models\Metadatas;

class MetadataSeeder extends DatabaseSeeder
{

    public function run(): void
    {
        //  Seeders to this Module
        // -------------------------------------------------------------------------------
        Metadatas::create([
            'name' => 'app_telegram_bot_owner',
            'value' => '816767995',
            'comment' => 'Propietario del bot de Telegram',
            'metadatatype' => 1,
            'is_visible' => 1,
        ]);
    }
}
