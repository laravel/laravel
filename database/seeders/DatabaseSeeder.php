<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\TestSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\TestAttemptSeeder;
use Database\Seeders\TestResultSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\InstitutionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            InstitutionSeeder::class,
            CategorySeeder::class,
            StudentSeeder::class,
            AdminSeeder::class,
            TestSeeder::class,
            QuestionSeeder::class,
            TestAttemptSeeder::class,
            TestResultSeeder::class,
        ]);
    }
}
