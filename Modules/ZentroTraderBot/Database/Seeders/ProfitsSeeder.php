<?php
namespace Modules\ZentroTraderBot\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\GutoTradeBot\Entities\Profits;

class ProfitsSeeder extends Seeder
{
    public function run()
    {
        Profits::create([
            'name' => "salary",
            'comment' => "Salario recibido por la gestion del capital: el 1% del capital recibido",
            'value' => 1,
        ]);
        Profits::create([
            'name' => "profit",
            'comment' => "Rendimiento que se debe obtener del capital recibido: el 7% del capital",
            'value' => 7,
        ]);
    }
}
