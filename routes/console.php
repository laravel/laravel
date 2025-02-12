<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\ConsoleOutput;

Artisan::command('inspire', function () {
    $output = new ConsoleOutput();
    $output->writeln(Inspiring::quote());
})->purpose('Display an inspiring quote');
