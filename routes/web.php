<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\TimeEntries\CreateTimeEntry;
use App\Livewire\Customers\CreateCustomer;
use App\Livewire\Customers\ListCustomers;
use App\Livewire\Projects\CreateProject;
use App\Livewire\Projects\ListProjects;
use App\Livewire\TimeEntries\ListAndEditTimeEntries;
use App\Providers\Filament\EmployeePanelProvider;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/time-entries/create', CreateTimeEntry::class)->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/customers', function () {
        return view('customers.index');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/projects', function () {
        return view('projects.index');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/time-entries', ListAndEditTimeEntries::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/employee');
    })->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
