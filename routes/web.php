<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;



// Temporary route to run migrations on cPanel
Route::get('/setup-database', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        
        // Optional: Run seeders if you have default data like admin user
        // Artisan::call('db:seed', ['--force' => true]); 
        
        return 'Database migration successful! All tables are created. You can now remove this route from routes/web.php';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/create-admin', function () {
    try {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@points.bd'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
            ]
        );
        return 'Admin user created successfully! Email: admin@points.bd | Password: password';
    } catch (\Exception $e) {
        return 'Error creating admin: ' . $e->getMessage();
    }
});
