<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FakePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 2;


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        try {

            $client = New Client(['http_errors' => false]);
            $response  = $client->request('POST', config('app.fake_post_url'));
            $code = $response->getStatusCode();
            
            if($code === 200)
                Log::debug('Request successfully');
            else
                log::debug(new Exception('The request can not be processed. Error code: '.$code));
            
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public function failed(Exception $exception)
    {
        Log::debug('The request can not be processed: '.$exception);
    }
}
