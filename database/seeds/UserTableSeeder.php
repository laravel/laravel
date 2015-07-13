<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->createDefaultUser();
        //$this->generateRandomUsers(29);
    }

    /**
     * Create a default user for your project
     *
     * @return void
     */
    protected function createDefaultUser()
    {
        factory(App\User::class)->create([
            'name' => 'Your name',
            'email' => 'your@email.com',
            'password' => bcrypt('secret'),
        ]);
    }

    /**
     * Generate N random users for testing purposes
     *
     * @param $total
     * @return void
     */
    protected function generateRandomUsers($total)
    {
        factory(App\User::class, $total)->create();
    }
}
