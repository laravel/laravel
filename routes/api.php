<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/user/email', function (Request $request) {
    return $request->user()->email;
});
Route::post('/user/name', function (Request $request) {
    return $request->user()->name;
});

Route::get('/users', function($request)
{
    return App\Models\User::all();
});

// update
Route::patch('/users', function($request) {
    foreach ( App\Models\User::all() as $user )
    {
        $user->update($request->all());
    }
});

Route::delete('/users', function($request, $id) {
    DB::select("DELETE from users_table WHERE id=" . $_GET['id']);
});