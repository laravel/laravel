<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdcutController extends Controller
{
    public function index()
    {
        return view('products.index');
    }
}
