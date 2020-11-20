<?php

namespace App\Domain\Home;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Shows the Home Page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('app/homepage');
    }
}
