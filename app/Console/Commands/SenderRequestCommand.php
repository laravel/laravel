<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Jobs\SendRequestJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SenderRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sender:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent a request to an endpoint';

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
        $job = (new SendRequestJob)
            ->onQueue('sender:request')
            ->delay(Carbon::now()->addSeconds(5));

        dispatch($job);
    }
}
