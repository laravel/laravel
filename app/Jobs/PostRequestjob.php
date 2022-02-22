<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Infraestructure\Requests\Post as PostRequestInfraestructure;

class PostRequestjob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->url = $data["url"];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $GouttleResponse = (new PostRequestInfraestructure)->Gouttle_Post_Request($this->url);

        echo $GouttleResponse;
    }


        /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        echo "job has been failed, retrying on 5 minutes, error context: ".$exception."";
    }
}
