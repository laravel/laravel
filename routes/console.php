<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/** @var Kernel $this */
Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
