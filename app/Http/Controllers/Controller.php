<?php

namespace App\Http\Controllers;

use Illuminate\{
    Foundation\Auth\Access\AuthorizesRequests,
    Foundation\Bus\DispatchesJobs,
    Foundation\Validation\ValidatesRequests,
    Routing\Controller as BaseController
};

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
