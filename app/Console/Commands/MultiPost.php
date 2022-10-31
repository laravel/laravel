<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MultiPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:multi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modo Prueba';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $count_notfound = 0;
        $count_forbidden = 0;
        $count_unauthorized = 0;
        $count_badrequest = 0;
        $count_internalerror = 0;
        $count_okay = 0;


        // We will run n times and once finish it will notify how many times it was successful and how many times it failed.
        for ($i = 0; $i < 100; $i++) {
            $response = Http::post('https://atomic.incfile.com/fakepost', []);
            // we will filter the request
            if ($response->getStatusCode() == 200) {
                $count_okay++;
            } else if ($response->getStatusCode() == 404) {
                $count_notfound++;
            } else if ($response->getStatusCode() == 403) {
                $count_forbidden++;
            } else if ($response->getStatusCode() == 401) {
                $count_unauthorized++;
            } else if ($response->getStatusCode() == 400) {
                $count_badrequest++;
            } else if ($response->getStatusCode() == 500) {
                $count_internalerror++;
            }
        }
      // information will be shown
        echo "Sucessfull Request: ".$count_okay ." \n".
        "Not Found: ".$count_notfound ." \n".
        "Forbidden: ".$count_forbidden ." \n".
        "Bad Request: ".$count_badrequest ." \n".
        "Internal Error: ".$count_internalerror." \n";
    }
}
