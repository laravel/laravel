<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InitProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all the necessary commands to make Laravel project ready to use';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Optional: serve after finishing
        $serve = $this->choice(
            "Would you like to serve after finishing?",
            ["No", "Yes"],
            1
        );

        //Get name for database
        $name = strtolower($this->ask('Name for database: ', config('app.name')));

        //Create .env file
        File::copy('.env.example', '.env');

        //Generate key
        $this->call('key:generate');

        //Put database name into .env
        file_put_contents('.env', preg_replace('/^DB_DATABASE=.+$/m', "DB_DATABASE=$name", file_get_contents('.env')));

        //Create database
        DB::statement("CREATE DATABASE IF NOT EXISTS $name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        //Migrate
        $this->call('migrate');

        //Seed
        $this->call('db:seed');

        //Serve
        if (strtolower($serve) == 'yes') {
            $this->call('serve');
        }

        return 0;
    }
}
