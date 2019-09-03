<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class postRequest extends Command
{
    /**
     * @var string for url to send request
     */
    private $url = "https://atomic.incfile.com/fakepost";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:post_request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command to send post request';

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
        try {
            /**
             * creation of parameters for http post
             */
            $http_params = array('http' =>
                array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                )
            );
            /**
             * creating context
             */
            $context = stream_context_create($http_params);
            $this->alert('Sending request to '.$this->url);
            /**
             * sending the context with the request
             */
            $result = file_get_contents($this->url, false, $context);
            /**
             * show results
             */
            $this->comment('Sent');
            $this->comment('This is the result of the http post request: --- '.$result." ---");
        }catch (\Exception $ex){
            /**
             * in case of error show the error
             */
            $this->error('Error in request http post error description:',$ex);
        }
    }
}
