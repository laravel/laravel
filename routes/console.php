<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/** @var Illuminate\Foundation\Console\Kernel $this */
Artisan::command('inspire', function () {
    /** @var Illuminate\Foundation\Console\ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
