<?php

namespace App\Domain\Home;

use App\Http\Controllers\Controller;

class LoginPageController extends Controller
{
    public function show()
    {
        return view('app/login');
    }
}
