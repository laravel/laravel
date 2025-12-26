<?php

use App\Http\Controllers\GuestEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GuestEntryController::class, 'index'])->name('home');
Route::post('/store', [GuestEntryController::class, 'store'])->name('guestbook.store');
