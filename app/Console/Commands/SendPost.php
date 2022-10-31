<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:post';

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

        $flag = 0;
        $warning = 0;
        // Request will keep running until post is successful
        while ($flag <= 1) {
            $response = Http::post('https://atomic.incfile.com/fakepost', []);
            $warning++;
            // If request keeps failing a warning alert will be displayed every 100 loops for user to consider.
            if ($warning > 0 && $warning % 100 == 0) {
                echo "Warning " . $warning . " attemps have already failed \n";
            }
            // Once Request is able to send a post flag counter will increase to 1
            if ($response->getStatusCode() == 200) {
                echo "Post was successful \n";
                $flag++;
            }
        }


    }
}
