<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::all(); 
        return view('grid', compact('links')); 
    }

    public function show(string $id)
    {
        return view('links.edit/{id}', [
            'user' => Link::findOrFail($id)
        ]);
    }
}
