<?php

namespace Modules\GutoTradeBot\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class GutoTradeBotDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(AccountsSeeder::class);
        $this->call(PenaltiesSeeder::class);
        $this->call(ProfitsSeeder::class);
    }
}
