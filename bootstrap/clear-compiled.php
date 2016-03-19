<?php

$files = [
    __DIR__.'/cache/compiled.php',
    __DIR__.'/cache/services.json', // Laravel 5.1
    __DIR__.'/cache/services.php', // Laravel 5.2
];

foreach ($files as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}
