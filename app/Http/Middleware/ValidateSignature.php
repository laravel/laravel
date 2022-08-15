<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    /**
     * The names of the parameters that should be ignored.
     *
     * @var array<int, string>
     */
    protected $ignore = [
        //'utm_campaign',
        //'utm_source',
        //'utm_medium',
        //'utm_content',
        //'utm_term',
        //'fbclid',
    ];
}
