<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::active()
            ->withCount('menuItems')
            ->paginate(12);

        return view('restaurants.index', compact('restaurants'));
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->load(['menuItems' => function($query) {
            $query->available()->with('category');
        }]);

        $categories = Category::whereHas('menuItems', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->getKey());
        })->get();

        return view('restaurants.show', compact('restaurant', 'categories'));
    }

    public function create()
    {
        return view('restaurants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_time' => 'required|integer|min:0',
            'minimum_order' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'delivery_fee' => $validated['delivery_fee'],
            'delivery_time' => $validated['delivery_time'],
            'minimum_order' => $validated['minimum_order'],
            'rating' => $validated['rating'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('restaurants', 'public');
        }

        Restaurant::create($data);

        return redirect()->route('restaurants.create')->with('success', 'Restaurant created successfully.');
    }
}
