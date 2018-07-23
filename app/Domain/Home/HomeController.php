<?php

namespace App\Domain\Home;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Loads the Home Page.
     *
     * @return \Illuminate\View\View
     */
    public function __invoke()
    {
        return view('app/home/home');
    }
}
