<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', 'root');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS inclouding');
    echo "Database created or exists.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
