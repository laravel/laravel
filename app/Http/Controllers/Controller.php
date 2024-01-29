<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function user() {
        return Auth::user();
    }
}
