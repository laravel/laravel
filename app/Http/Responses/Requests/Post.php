<?php

namespace App\Http\Responses\Requests;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Post
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function Success_Dispatched_Job() {

        $Response = "Success New Request Job, queuing job...";

        return $Response;
        
    }

    public function Error_Dispatched_Job() {

        $Response = "Error Request, Cannot Reach url parameter inside the body...";

        return $Response;
        
    }


    public function Error_Empty_Url_Validation() {

        $Response = "Failed Url Validation, Url empty";

        return $Response;
        
    }

    public function Exist_Error_Url_Validation() {

        $Response = "Variable is not a valid URL, not queueing job...";

        return $Response;
        
    }

    public function Success_200($Request_Status) {

        $Response = "Success Request ".$Request_Status.", queuing job request";

        return $Response;
        
    }

    public function Error($Err_status) {

        $Response = "Failed Request ".$Err_status.", not queueing Job, retrying...";

        return $Response;
        
    }
}
