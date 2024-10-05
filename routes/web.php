<?php

use Illuminate\Support\Facades\Route;
use App\Models\Link;
use Illuminate\Http\Request;
use App\Http\Controllers\ColorController;

Route::get('/', function(){
    return redirect()->route('links.index');
});


Route::view('/grid/create','create')
->name('links.create');

Route::get('/grid', function ()  {
    return view('index',['links'=>Link::all()]);
})->name('links.index');

Route::delete('/grid/{link}', function (Link $link) {
    $link->delete();
    return redirect()->route('links.index')
        ->with('success', 'Link Deleted Successfully!');
})->name('links.destroy');


Route::post('/grid/create', function(Request $request){
    $data = $request;
    $link = new Link;
    $link->title = $data['title'];
    $link->page = $data['page'];
    $link->color = $data['color'];
    $link->save();

    return redirect()->route('links.index');
})->name('links.store');

Route::put('/grid/edit/{id}', function(Request $request ,$id){
    $data = $request;
    $link = Link::findorfail($id);
    $link->title = $data['title'];
    $link->page = $data['page'];
    $link->color = $data['color'];
    $link->save();

    return redirect()->route('links.index');
})->name('links.update');


Route::get('grid/{id}', function($id){
    return view('show', ['link'=> Link::findorfail($id)]);
})->name('links.show');


Route::get('grid/{id}/edit', function($id){
    return view('edit', ['link'=> Link::findorfail($id)]);
})->name('links.edit');
