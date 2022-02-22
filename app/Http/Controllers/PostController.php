<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Services\Requests\Post as PostRequestService;

use Request;

class PostController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function Post( Request $request ){

        $Response = (new PostRequestService)->DispatchingRequest($request);

        return response()
        ->json([
            'response_message' => $Response,
        ]);

    }
}
