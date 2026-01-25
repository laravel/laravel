<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function selling()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('selling', compact('products'));
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock >= $request->quantity) {
            $product->decrement('stock', $request->quantity);
            return redirect()->route('selling')->with('success', 'Sale completed!');
        }
        
        return redirect()->route('selling')->with('error', 'Insufficient stock!');
    }

    public function manage()
    {
        $products = Product::all();
        return view('manage', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        Product::create($request->all());
        return redirect()->route('manage')->with('success', 'Product added!');
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->increment('stock', $request->quantity);
        
        return redirect()->route('manage')->with('success', 'Stock updated!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('manage')->with('success', 'Product removed!');
    }

    public function viewStore()
    {
        $products = Product::all();
        return view('store', compact('products'));
    }
}
