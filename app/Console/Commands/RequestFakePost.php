<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class RequestFakePost extends Command
{
    /**
     * The name and signature of the console command (adding an optional argument to determinate wich interview process to do).
     *
     * @var string
     */
    protected $signature = 'request:fake {processOption?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request fake post';

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
        //Check if has command argument included, if so, it will execute requesting function
        //depending on the argument is the number of requests to execute
        //4 (interview process number 4) 
        //5 (interview process number 5) 
        if($this->argument('processOption')=='4')
            $this->sendRequest('https://atomic.incfile.com/fakepost',1);
        else if($this->argument('processOption')=='5')
            $this->sendRequest('https://atomic.incfile.com/fakepost',1000);
        else
            $this->info('Argument required to execute request,(accepted only:4 or 5), example:"php artisan request:fake 4"');
    }

    //Private function to execute requests
    //This function is used to handle http request failiure exception and it is used to run service as many times as the argument indicates
    //Processes number 4 and 5 are handled with this function to avoid repetitive code
    //Guzzle PHP HTTP Client Library is used to optimize code and to handle async multiple requests
    private function sendRequest($url,$total){
            $client = new Client();
            $requests = function ($total) use ($url){
                $uri = $url;
                for ($i = 0; $i < $total; $i++) {
                    yield new Request('POST', $uri);
                }
            };
            $pool = new Pool($client, $requests($total), [
                'concurrency' => 6,
                'fulfilled' => function (Response $response, $index) {
                    $this->info('Request number '.($index+1).' - Response: Successful request');
                },
                'rejected' => function (RequestException $e, $index) {
                    $this->comment('Request number '.($index+1).' - Response: The API:"https://atomic.incfile.com/fakepost" is not available now');
                },
            ]);
            $promise = $pool->promise();
            $promise->wait();
    }
}
