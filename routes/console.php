<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () use ($command) {
    $command->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
