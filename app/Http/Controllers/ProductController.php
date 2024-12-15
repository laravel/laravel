<?php

namespace App\Http\Controllers;

use App\Models\Coffee;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Coffee::where('is_available', true)
            ->orderBy('name')
            ->get();
            
        return view('dashboard', compact('products'));
    }
} 