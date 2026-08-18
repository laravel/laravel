<?php

use App\Http\Controllers\Api\FinanceController; use App\Http\Controllers\ReportController; use App\Http\Controllers\Web\{AdminController,AuthController,DashboardController,OperationsController}; use Illuminate\Support\Facades\Route;
Route::get('/shared/receipts/{receipt}/pdf',[FinanceController::class,'receipt'])->middleware('signed')->name('receipts.shared');
Route::get('/shared/invoices/{invoice}/pdf',[FinanceController::class,'invoicePdf'])->middleware('signed')->name('invoices.shared');
Route::redirect('/','/dashboard'); Route::middleware('guest')->group(function(){Route::get('/login',[AuthController::class,'form'])->name('login');Route::post('/login',[AuthController::class,'login'])->middleware('throttle:5,1');
 Route::get('/forgot-password',[AuthController::class,'forgotPasswordForm'])->name('password.request');Route::post('/forgot-password',[AuthController::class,'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
 Route::get('/reset-password/{token}',[AuthController::class,'resetPasswordForm'])->name('password.reset');Route::post('/reset-password',[AuthController::class,'resetPassword'])->name('password.update');
});
Route::middleware(['auth','active'])->group(function(){Route::post('/logout',[AuthController::class,'logout'])->name('logout');Route::get('/dashboard',DashboardController::class)->name('dashboard');
 Route::get('/companies',[AdminController::class,'companies']);Route::post('/companies',[AdminController::class,'storeCompany']);
 Route::get('/buildings',[AdminController::class,'buildings']);Route::post('/buildings',[AdminController::class,'storeBuilding']);Route::post('/buildings/{building}',[AdminController::class,'updateBuilding']);
 Route::get('/rooms',[AdminController::class,'rooms']);Route::post('/rooms',[AdminController::class,'storeRoom']);Route::post('/rooms/{room}',[AdminController::class,'updateRoom']);
 Route::get('/customers',[AdminController::class,'customers']);Route::post('/customers',[AdminController::class,'storeCustomer']);Route::post('/customers/{customer}',[AdminController::class,'updateCustomer']);
 Route::get('/meal-plans',[AdminController::class,'plans']);Route::post('/meal-plans',[AdminController::class,'storePlan']);Route::post('/meal-plans/{plan}',[AdminController::class,'updatePlan']);
 Route::get('/employees',[AdminController::class,'employees']);Route::post('/employees',[AdminController::class,'storeEmployee']);Route::post('/employees/{employee}/toggle',[AdminController::class,'toggleEmployee']);
 Route::get('/deliveries',[OperationsController::class,'deliveries']);Route::get('/invoices',[OperationsController::class,'invoices']);Route::get('/payments',[OperationsController::class,'payments']);Route::get('/balances',[OperationsController::class,'balances']);Route::get('/cash-handovers',[OperationsController::class,'handovers']);Route::get('/daily-closings',[OperationsController::class,'closings']);Route::get('/payment-corrections',[OperationsController::class,'corrections']);Route::get('/notifications',[OperationsController::class,'notifications']);Route::get('/audit-log',[OperationsController::class,'audit']);Route::get('/devices',[OperationsController::class,'devices']);Route::get('/reports',[OperationsController::class,'reports']);Route::get('/reports/{report}/{format}',[ReportController::class,'export']);Route::get('/settings',[OperationsController::class,'settings']);Route::put('/settings',[OperationsController::class,'updateSettings']);
});
