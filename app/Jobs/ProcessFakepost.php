<?php

namespace App\Jobs;

use Exception;
use App\Events\Fakepost\Succeeded;
use App\Events\Fakepost\Failed;
use Facades\App\Common\HttpClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFakepost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The URL to reach.
     *
     * @var string
     */
    CONST URL = 'https://atomic.incfile.com/fakepost';

    /**
     * Expected HTTP code that define a successful request.
     *
     * @var int
     */
    CONST EXPECTED_HTTP_CODE = 200;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::debug('Executing ProcessFakepost');
            $response = HttpClient::post(self::URL);
            $responseCode = $response->getStatusCode();
            $body = $response->getBody();

            if( $responseCode === self::EXPECTED_HTTP_CODE ) {
                event(new Succeeded($body));
            } else {
                event(new Failed(new Exception("Something went wrong http code {$responseCode}")));
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        event(new Failed($exception));
    }
}
