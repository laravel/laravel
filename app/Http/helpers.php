<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Global Helpers
|--------------------------------------------------------------------------
|
| Here is where you can register helper functions for your application.
| These helper functions can be uesed anywhere in you application
| Now create something great!
|
*/

if (! function_exists('inspire')) {
    /**
     * return an inspinring quote
     *
     * @return string
     */
    function inspire()
    {
        return Inspiring::quote();
    }
}