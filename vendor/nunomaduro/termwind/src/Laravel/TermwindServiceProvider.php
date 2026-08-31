<?php

declare(strict_types=1);

namespace Termwind\Laravel;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;
use Termwind\Termwind;

final class TermwindServiceProvider extends ServiceProvider
{
    /**
     * Sets the correct renderer to be used.
     */
    public function register(): void
    {
        $this->app->resolving(OutputStyle::class, function ($style): void {
            Termwind::renderUsing($style->getOutput());
        });
    }
}
