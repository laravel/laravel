<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
 * Overview (with the same Controller (ExampleController) behind the routes):
 * /folder/group --> WORKS
 * /folder/comparison --> WORKS
 *
 * /folder/group/create --> FAILS
 * /folder/comparison/create --> WORKS
 *
 * /folder/group/1 --> FAILS
 * /folder/comparison/1 --> WORKS
 *
 * /folder/group/1/edit --> FAILS
 * /folder/comparison/1/edit --> WORKS
 */

Route::group(['prefix' => 'folder'], function () {

    Route::group(['prefix' => 'group'], function () {

        // The routes to the controller which contains an issue
        Route::resource('', 'ExampleController');

    });

    // The routes to the same controller without the issue
    Route::resource('/comparison', 'ExampleController');

});