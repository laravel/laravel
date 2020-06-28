<?php

namespace App\Jobs;

use App\Events\ClientIssuesEvent;
use Illuminate\Bus\Queueable;
use App\Events\ServerIsDownEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SendRequestJob implements ShouldQueue
{
    private const LIMIT_TRYING = 3;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $configurationJson = Storage::disk('private')
        //     ->get('requestDefault.json');
        // $configuration = json_decode($configurationJson);

        // $client = new Client();
        // try {
        //     $response = $client->request($configuration->method, $configuration->route);
        // } catch (ServerException $e) {
        //     event(new ServerIsDownEvent($e->getResponse()));
        //     return;
        // } catch (RequestException $e) {
        //     event(new ClientIssuesEvent($e->getResponse()));

        //     return;
        // }

        // Log::info($response->getBody()->getContents());
    }

    public function retryUntil()
    {
        return now()->addSeconds(3);
    }
}
