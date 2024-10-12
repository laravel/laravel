<?php

namespace App\Traits;

trait FormatQueryString
{
    public function format(string $uri, array $params = []): string
    {
        if (empty($params)) {
            return $uri;
        }

        $uri .= '?';
        $queryString = '';

        array_walk($params, function ($value, $key) use (&$queryString) {
            $queryString .= $key . '=' . $value . '&';
        });

        $queryString = rtrim($queryString, '&');

        return $uri . $queryString;
    }
}
