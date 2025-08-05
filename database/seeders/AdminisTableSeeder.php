<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Adminis;
use Hash;

class AdminisTableSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('123456');

        $admin = new Adminis;
        $admin->name = 'Amit Gupta';
        $admin->role = 'admin';
        $admin->mobile = '9800000000';
        $admin->email = 'admin@admin.com';
        $admin->password = $password;
        $admin->status = 1;
        $admin->save();
    }
}
