<?php

use App\Models\User;

$user = new User();
$user->name = 'Admin';
$user->email = 'admin@terradomeio.com.br';
$user->password = bcrypt('admin123');
$user->save();
echo "Admin user created: admin@terradomeio.com.br / admin123\n";
