<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFakepost;
use Illuminate\Console\Command;

class fakepost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fakepost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger ProcessFakepost job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ProcessFakepost::dispatch();
    }
}
