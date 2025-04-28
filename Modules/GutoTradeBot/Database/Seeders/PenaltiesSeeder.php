<?php
namespace Modules\GutoTradeBot\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\GutoTradeBot\Entities\Penalties;

class PenaltiesSeeder extends Seeder
{
    public function run()
    {
        Penalties::create([
            'from' => 0,
            'to' => 49.99,
            'amount' => 20,
        ]);
        /*
    Penalties::create([
    'from' => 0,
    'to' => 29.99,
    'amount' => 100,
    ]);
    Penalties::create([
    'from' => 30,
    'to' => 49.99,
    'amount' => 80,
    ]);
    Penalties::create([
    'from' => 50,
    'to' => 79.99,
    'amount' => 40,
    ]);
    Penalties::create([
    'from' => 80,
    'to' => 99.99,
    'amount' => 20,
    ]);
     */
    }
}
