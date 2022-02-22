<?php

namespace App\Http\Infraestructure\Requests;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Responses\Requests\Post as PostRequestReponses;

class Post
{

    public $response;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function Gouttle_Post_Request($url) {

        try {
                  
            $client = new \GuzzleHttp\Client();

            $this->response = $client->request('POST', $url);
    
        // Catch Exception
        } catch (\GuzzleHttp\Exception\RequestException $e) {

            $guzzleResult = $e->getResponse();

            $GouttleResponse = (new PostRequestReponses)->Error($e->getCode());

            return $GouttleResponse;
        }

        $GouttleResponse = (new PostRequestReponses)->Success_200($this->response->getStatusCode());

        return $GouttleResponse;
        

    }
}
