<?php

namespace App\Http\Utils;

use Illuminate\Http\Request;

abstract class BaseRequest
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function request(): Request
    {
        return $this->request;
    }
}
