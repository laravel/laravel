<?php

// Use statements to include necessary classes and facades.
// 'Inspiring' provides a collection of inspiring quotes.
// 'Artisan' is the facade for the Artisan command-line interface.

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Define a new Artisan command using the Artisan::command method.
// This command is named 'inspire' and when executed, it will display
// an inspiring quote to the console.

Artisan::command('inspire', function () {
    // Output an inspiring quote to the console.
    // The $this->comment method is used to display the quote in the console.
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
