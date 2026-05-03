<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('category')
            ->get();

        $categories = Category::with(['products' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        return view('welcome', compact('featuredProducts', 'categories'));
    }
}
