<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProdcutController extends Controller
{
    public function index()
    {
        return view('product.index');
    }
    public function create()
    {
        return view('product.create');
    }
    public function store(request $request)
    {
        $data = $request->validate(
            [
                'name' => 'required',
                'qty' => 'required','numeric',
                'price' => 'required','decimal',

            ]);
            $newProduct = Product::create($data);
            return redirect()->route('product.index');
    }
}
