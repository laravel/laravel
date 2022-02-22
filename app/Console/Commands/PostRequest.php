<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Services\Requests\Post as PostRequestService;
use App\Http\Responses\Requests\Post as PostRequestReponses;

class PostRequest extends Command
{

    public $url;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Post:Request {url?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to Make a Post Request';

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

        $Arguments = $this->arguments();

            $Response = (new PostRequestService)->MakeRequest($Arguments["url"]);

            if(!empty($Response)){
                $this->info($Response);
            }


    }
}
