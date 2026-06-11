<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;


class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('order')->get();
        $featuredItems = MenuItem::with(['restaurant', 'category'])
            ->featured()
            ->available()
            ->limit(6)
            ->get();
        $restaurants = Restaurant::active()->withCount('menuItems')->paginate(12);

        return view('home', compact('categories', 'featuredItems', 'restaurants'));
    }
}
