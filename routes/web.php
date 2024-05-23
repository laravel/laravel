<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbstractUploadController;
use App\Http\Middleware\SuperAdmin;
use App\Http\Controllers\AbstractReviewController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/thank-you', function () {
    return view('auth.thank-regis');
})->name('auth.thank-regis');


Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'create']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Profile Route (Protected)
Route::middleware(['auth'])->get('/profile', [ProfileController::class, 'show'])->name('profile');

Route::get('/admin/user', [AdminController::class, 'profileView'])->name('admin.profile')->middleware('auth');

Route::get('/activate/{user}', [ActivationController::class, 'activate'])->name('activate')->middleware('signed');

Route::get('/activation/error', [ActivationController::class, 'error'])->name('activation.error');

Route::post('/password/change', [ProfileController::class, 'changePassword'])->name('password.change');


// Route::middleware(SuperAdmin::class)->group(function () {
//     Route::get('/super_admin/dashboard', [SuperAdminController::class, 'dashboard']);
// });
// Route::middleware(SuperAdmin::class)->group(function () {
//     // Routes accessible only to super admins
//     Route::get('/super_admin/dashboard', function () {
//         return view('super_admin.dashboard');
//     });
// });


Route::middleware(SuperAdmin::class)->group(function () {
    
    Route::get('/super_admin/userlist', function () {
        // Fetch the super admin name here
        $superAdminName = auth()->user()->name;

        // Fetch non-super-admin users
        $nonSuperAdminUsers = User::where('role', '!=', 'super_admin')->get();

        // Pass the list of non-super-admin users to the userlist view
        return view('super_admin.userlist', [
            'nonSuperAdminUsers' => $nonSuperAdminUsers,
            'superAdminName' => $superAdminName
        ]);
    });

    Route::get('/super_admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');
    
    Route::get('/super_admin/abstractreview', [AbstractReviewController::class, 'abstractReview'])->name('super_admin.abstractreview');

    Route::post('/super_admin/update-abstract-status', [SuperAdminController::class, 'updateAbstractStatus'])->name('super_admin.update.abstract.status');
    
    Route::get('/super_admin/export_abstracts', [SuperAdminController::class, 'exportAbstracts'])->name('export.abstracts');
    Route::post('/super_admin/import_abstracts', [SuperAdminController::class, 'importAbstracts'])->name('import.abstracts');

});


// Route::get('/admin/abstract', 'AbstractUploadController@abstractForm')->name('admin.abstract');
Route::get('/admin/abstract', [AbstractUploadController::class, 'abstractForm'])->name('admin.abstractupload');
// Route::post('/admin/abstract', 'AbstractUploadController@storeAbstract')->name('admin.abstract.store');
Route::get('/super_admin/abstractreview', [AbstractReviewController::class, 'abstractReview'])->name('super_admin.abstractreview');
Route::get('/download-abstracts', [SuperAdminController::class, 'downloadAbstracts'])->name('download.abstracts');



Route::get('abstract-upload/create', [AbstractUploadController::class, 'create'])->name('abstract-upload.create');

Route::post('/abstract-upload/store', [AbstractUploadController::class, 'store'])->name('abstract-upload.store');

Route::delete('/abstract-upload/{id}', [AbstractUploadController::class, 'destroy'])->name('abstract-upload.destroy');

// Forgot Password Route
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Reset Password Routes
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');  

// Route::get('/super_admin/dashboard', [SuperAdminDashboardController::class, 'viewdashboard'])->name('super_admin.dashboard');