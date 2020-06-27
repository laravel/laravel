<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\Response;

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
        $configurationJson = Storage::disk('private')
            ->get('requestDefault.json');
        $configuration = json_decode($configurationJson);

        /** @var Response $response */
        $response = Http::get($configuration->route);
        echo $response->body();
    }
}
