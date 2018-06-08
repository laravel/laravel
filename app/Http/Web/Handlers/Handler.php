<?php

namespace App\Http\Web\Handlers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseHandler;

abstract class Handler extends BaseHandler
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
}
