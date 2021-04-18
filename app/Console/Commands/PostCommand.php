<?php

namespace App\Console\Commands;

use App\Jobs\ProcessRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send post request';

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
     * @return int
     */
    public function handle()
    {
        ProcessRequest::dispatch('https://atomic.incfile.com/fakepost')->onQueue('posts');
    }
}
