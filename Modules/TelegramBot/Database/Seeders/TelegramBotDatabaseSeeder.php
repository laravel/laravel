<?php

namespace Modules\TelegramBot\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class TelegramBotDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");

        $this->call(ActorsSeeder::class);

        $this->call(TelegramBotsSeeder::class);
    }
}
