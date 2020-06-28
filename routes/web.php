<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

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

Route::get('/testing', function () {
    $client = new GuzzleHttp\Client();
    try {

        $res = $client->request('GET', 'https://api.github.com/user');
    } catch (ClientException $e) {
    } catch (ServerException $e) {
        var_dump($e->getResponse()->getStatusCode());
        return;
    }
    var_dump('pase');
});
