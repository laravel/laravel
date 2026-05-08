<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('admin.products', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'price'   => 'required|numeric|min:0',
            'stock'   => 'required|numeric|min:0',
            'capital' => 'nullable|numeric|min:0',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'         => $request->name,
            'price'        => (int) round($request->price),
            'stock'        => (int) round($request->stock),
            // ✅ FIX: Gamitin ang filled() para ma-detect ang "0" na value
            'capital'      => $request->filled('capital') ? (int) round($request->capital) : 0,
            'is_available' => true,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully.',
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'price'   => 'required|numeric|min:0',
            'stock'   => 'required|numeric|min:0',
            'capital' => 'nullable|numeric|min:0',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'price' => (int) round($request->price),
            'stock' => (int) round($request->stock),
            // ✅ FIX: Gamitin ang filled() — kaya kahit 0 ang input, ma-save pa rin
            'capital' => $request->filled('capital') ? (int) round($request->capital) : $product->capital,
        ];

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => $product->fresh(),
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted.']);
    }
}