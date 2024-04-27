<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CustomersController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/', function () {
    return view('employee');
});
Route::post('employee-add', [EmployeeController::class, 'employee_add']);
Route::get('employee-view', [EmployeeController::class, 'employee_view']);
Route::get('employee-delete', [EmployeeController::class, 'employee_delete']);
Route::post('employee-edit', [EmployeeController::class, 'employee_edit']);
Route::get('employee-list', [EmployeeController::class, 'employee_list']);

Route::get('/Customers', function () {
    return view('Customers');
});
Route::post('customers-add', [CustomersController::class, 'customers_add']);
Route::get('customers-view', [CustomersController::class, 'customers_view']);
Route::get('customers-delete', [CustomersController::class, 'customers_delete']);
Route::post('customers-edit', [CustomersController::class, 'customers_edit']);
Route::get('customers-list', [CustomersController::class, 'customers_list']);


require __DIR__.'/auth.php';
