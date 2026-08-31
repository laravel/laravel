<?php

namespace Illuminate\Foundation\Exceptions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

class RegisterErrorViewPaths
{
    /**
     * Register the error view paths.
     *
     * @return void
     */
    public function __invoke()
    {
        View::replaceNamespace('errors', (new Collection(config('view.paths')))
            ->map(fn ($path) => "{$path}/errors")
            ->push(__DIR__.'/views')
            ->all()
        );
    }
}
