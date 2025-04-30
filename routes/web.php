<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend');
});


Route::get('/cache', function () {
    echo 'Intentando borrar cache<br/>';
    $array = array(
        'route:cache',
        'cache:clear',
        'config:clear',
        'config:cache',
        'view:clear',
        'optimize',
        /* 
        Comente esto porq me estaba dando error luego en localhost 
        la solucion era ejecutar manualmente composer dumpautoload 
        */
        //'optimize:clear',  
        'route:clear',
    );
    foreach ($array as $line) {
        $code = Artisan::call($line);
        echo $line . ': ' . $code . '<br/>';
    }
    die('Hecho!');
});
Route::get('/test/{name?}', 'App\Http\Controllers\TestController@test')->name('test-byname');

Route::prefix('logs')->group(function () {
    Route::get('/clear', 'App\Http\Controllers\FileController@clearLog')->name('clear-logs');
    Route::get('/{type?}/{amount?}', 'App\Http\Controllers\FileController@readLog')->name('read-logs');
});
Route::get('/report/{format}/{name}', 'App\Http\Controllers\FileController@renderAndDestroy')->name('report-byname');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
