<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')->name('dashboard');
Route::view('/snaps', 'snaps')->name('snaps');
Route::view('/user-management', 'user-management-dashboard')->name('user-management.dashboard');
Route::view('/group-management', 'group-management')->name('group-management');
Route::view('/billing', 'billing')->name('billing');
Route::view('/settings', 'settings')->name('settings');
Route::view('/users', 'users')->name('users');
Route::view('/user-details', 'user-details')->name('user-details');
Route::view('/design-system', 'design-system')->name('design-system');
Route::view('/login', 'login')->name('login');
