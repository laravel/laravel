<?php

namespace Modules\ZentroPackageBot\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\ZentroPackageBot\Database\Seeders\PackagesSeeder;

class ZentroPackageBotDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(PackagesSeeder::class);
    }
}
