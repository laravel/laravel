<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Bob;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});

// Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Protected route - requires authentication
Route::get('/dashboard', function () {
    return "Welcome to your dashboard! You are logged in.";
})->middleware('auth');

// Protected route - show profile
Route::get('/profile', function () {
    $user = auth()->user();
    return "Profile: " . $user->email;
})->middleware('auth');

Route::get('/test', function(){
    $person = new Bob();
    $person->name = "John";
    $person->age = 25;
    $person->save();
    
    return "Person created!";
});

Route::get('/showall', function(){
    $people = Bob::all();
    var_dump($people);
});

// Update a person (change ID 1's name and age)
Route::get('/update/{id}', function($id){
    $person = Bob::find($id);
    
    if($person){
        $person->name = 'Updated Name';
        $person->age = 30;
        $person->save();
        return "Person updated!";
    }
    
    return "Person not found!";
});

// Delete a person
Route::get('/delete/{id}', function($id){
    $person = Bob::find($id);
    
    if($person){
        $person->delete();
        return "Person deleted!";
    }
    
    return "Person not found!";
});

// Find one person by ID
Route::get('/find/{id}', function($id){
    $person = Bob::find($id);
    
    if($person){
        var_dump($person);
    } else {
        return "Person not found!";
    }
});

// Find people with conditions (e.g., name contains "John")
Route::get('/search', function(){
    $people = Bob::where('name', 'like', '%John%')->get();
    var_dump($people);
});

// Store a new person (POST)
Route::post('/store', function(Request $request){
    $name = $request->name;
    $adres = $request->adres;
    $age = $request->age;
    
    $person = new Bob();
    $person->name = $name;
    $person->adres = $adres;
    $person->age = $age;
    $person->save();
    
    return "Person stored! ID: " . $person->id;
});

// Update a person (POST)
Route::post('/update/{id}', function(Request $request, $id){
    $person = Bob::find($id);
    
    if($person){
        $person->name = $request->name ?? $person->name;
        $person->adres = $request->adres ?? $person->adres;
        $person->age = $request->age ?? $person->age;
        $person->save();
        
        return "Person updated!";
    }
    
    return "Person not found!";
});