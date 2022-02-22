<?php

namespace App\Http\Helpers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Verify
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function url($url) {

        $url_validated = Str::contains($url, ['http', 'https']);

        return $url_validated;
        
    }


}
