<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;

class TestExceptionHandling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:error';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fires a test exception';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        throw new Exception('Test exception!');
    }
}
