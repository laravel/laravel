<?php

declare(strict_types=1);

namespace App;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    protected $namespace = 'App\\';

    public function __construct($basePath = null)
    {
        parent::__construct($basePath);

        /**
         * Overwriting the app_path to autoload commands correctly.
         *
         * @see https://github.com/regnerisch/laravel-beyond/issues/66
         */
        $this->useAppPath($basePath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'App');
    }
}
