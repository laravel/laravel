<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home.index', []);
})-> name('home.index');

Route::get('/contact', function(){
    return view('home.contact', []);
})-> name('home.index');

Route::get('/posts/{id}', function($id){
    return 'Blog posts ' . $id;
})-> where([
    'id' => '[0-9]+'
    ])
-> name('blog.id');

Route::get('/recent-post/{day_ago?}', function($day_ago = 10){
    return  'Post from '. $day_ago . ' day ago';
})-> name('recent-post.day_ago');


