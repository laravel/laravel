<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FakePostJob;

class FakePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incfile:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a post request to incfile url';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        FakePostJob::dispatch();
    }
}
