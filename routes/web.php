<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
