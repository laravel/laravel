<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreateAdminUserSeeder extends Seeder
{
    
        /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin', 
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'password' => 'admin123'
        ]);

        // // $role = Role::create(['name' => 'admin']);

        // // $permissions = Permission::pluck('id','id')->all();

        // $role->syncPermissions($permissions);

        // $user->assignRole([$role->id]);
    }
    
}
