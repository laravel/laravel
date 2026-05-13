<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::updateOrCreate(
    ['email' => 'sujan@points.bd'],
    [
        'name' => 'Sujan',
        'password' => Hash::make('password123'),
        'role' => 'admin'
    ]
);

echo "Created user: " . $user->email . " with password: password123\n";
