<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('permissions')->insert([
            'type' => 'Read'
        ]);

        DB::table('permissions')->insert([
            'type' => 'Write'
        ]);
        
        DB::table('permissions')->insert([
            'type' => 'Update'
        ]);

        DB::table('permissions')->insert([
            'type' => 'Delete'
        ]);
    }
}
