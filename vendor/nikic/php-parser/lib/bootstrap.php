<?php

require dirname(__FILE__) . '/PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

/*
 * lcfirst() was added in PHP 5.3, so we have to emulate it for PHP 5.2.
 */
if (!function_exists('lcfirst')) {
    function lcfirst($string) {
        $string[0] = strtolower($string[0]);
        return $string;
    }
}
