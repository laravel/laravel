<?php

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



Route::get('/datatables/main' , 'Frontend\Datatables\ExamController@index');                                      //1e doe dit erbij
Route::post('/datatables/Exameditor' , 'Frontend\Datatables\ExamController@Exameditor')->name('Exameditor');      //let op Route::POST
Route::get('/datatables/getExamAjax' , 'Frontend\Datatables\ExamController@getExamAjax')->name('getExamAjax');

Route::Resource('/datatables' , 'Frontend\Datatables\ExamController');          //1e bij resource komt de post route altijd eerst , anders nginx error
