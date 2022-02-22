<?php

namespace App\Http\Services\Requests;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Validator;

use App\Http\Helpers\Verify as Verify_Helper;
use App\Http\Infraestructure\Requests\Post as PostRequestInfraestructure;
use App\Http\Responses\Requests\Post as PostRequestReponses;

use App\Jobs\PostRequestjob;

class Post
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function MakeRequest($url) {

        // Validate if argument Exist

        $ValidateArguments = Validator::make([
            'url' => $url,
        ], [
            'url' => ['required'],
        ]);

        if ($ValidateArguments->fails()) {

         $ValidateResponse = (new PostRequestReponses)->Error_Empty_Url_Validation($url);

         return $ValidateResponse;

        }

        // Validate if is a valid URL

        $validated_url = (new Verify_Helper)->url($url);

        // If URL is not a valid url return error response

        if($validated_url == FALSE){

            $GouttleResponse = (new PostRequestReponses)->Exist_Error_Url_Validation();
            
            return $GouttleResponse;

        }

        // If URL is a valid url send to request function

        $GouttleResponse = (new PostRequestInfraestructure)->Gouttle_Post_Request($url);

        return $GouttleResponse;

    }

    public function DispatchingRequest($request) {

        $data = json_decode(request()->getContent(), true);

        // Validate if argument Exist

        $ValidateArguments = Validator::make([
             'url' => $data["url"],
            ], [
             'url' => ['required'],
            ]);
        
        if ($ValidateArguments->fails()) {
        
            $ValidateResponse = (new PostRequestReponses)->Error_Empty_Url_Validation($data["url"]);
        
            return $ValidateResponse;
        
        }

        // Validate if is a valid URL

        $validated_url = (new Verify_Helper)->url($data["url"]);

        // If URL is not a valid url return error response

        if($validated_url == FALSE){

            $GouttleResponse = (new PostRequestReponses)->Exist_Error_Url_Validation();
            
            return $GouttleResponse;

        }

        if(empty($data)) {

            $GouttleResponse = (new PostRequestReponses)->Error_Dispatched_Job();

            return $GouttleResponse;

        }

        PostRequestjob::dispatch(array(
            'url' => $data["url"],
        ))->onConnection('database');

        $GouttleResponse = (new PostRequestReponses)->Success_Dispatched_Job();

        return $GouttleResponse;

    }
}
