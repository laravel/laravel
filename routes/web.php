<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataDiriController;
use function Laravel\Ai\{agent};
use illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/datadiri', [DataDiriController::class, 'index']);

Route::get('/ai', function () {
    $response = agent(
        instructions: 'Kamu adalah asisten AI yang helpful',
    )->prompt('Buatkan sebuah jokes bapak bapak malam hari');

    $html = Str::markdown((string) $response);

    return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>AI Generated Page</title>
            <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            background: #ffffff;
            padding: auto;
            border-radius: auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: auto;
        }
        h2 {
            margin-top: 0;
            color: #1f2937;
        }
        p {
            margin: 8px 0;
            color: #4b5563;
        }
        ul {
            padding-left: 20px;
            color: #374151;
        }
    </style>
        </head>
        <body>
            <div class="card">
                {$html}
            </div>
        </body>
        </html>
    HTML;
});