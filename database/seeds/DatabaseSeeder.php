<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed users table (if needed)
        // $this->call(UsersTableSeeder::class);
        
        // Seed employees table
        $this->call(EmployeeSeeder::class);
    }
}
