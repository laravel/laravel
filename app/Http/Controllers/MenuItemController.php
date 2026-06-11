<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Restaurant;

class MenuItemController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $restaurants = Restaurant::orderBy('name')->get();
    
        return view('menuitems.create', compact('categories', 'restaurants'));
    }

    public function show(MenuItem $menuItem)
    {
        $menuItem->load(['restaurant', 'category']);

        return view('menuitems.show', compact('menuItem'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurant,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'sometimes|boolean',
            'is_vegetarian' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);

        $data = [
            'restaurant_id' => $validated['restaurant_id'],
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_available' => $request->has('is_available'),
            'is_vegetarian' => $request->has('is_vegetarian'),
            'is_featured' => $request->has('is_featured'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu_items', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('menuitems.create')->with('success', 'Menu item has been saved.');
    }
}
