<?php

// Test script untuk login
require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

echo "Testing AuthController...\n";

// Create mock request
$request = new Illuminate\Http\Request();
$request->merge([
    'username' => 'admin',
    'password' => 'admin123'
]);

try {
    $controller = new AuthController();
    echo "AuthController instantiated successfully\n";
    
    // Test login method
    $result = $controller->login($request);
    echo "Login method executed successfully\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
