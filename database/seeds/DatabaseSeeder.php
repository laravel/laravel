<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        factory(App\User::class, 100)->create();                     // database/factories/ExamFactory     equals number of user_id
        factory(App\Models\DataTables\Exam::class, 200)->create();  //as much as you want

    }
}
