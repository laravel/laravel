<?php

if (! function_exists('jdd')) {
    /**
     * JSON Dump and Die.
     * Return a JSON response and immediately stops program execution.
     *
     * @param  mixed  $data  The data to convert to JSON and send as response.
     * @param  int|null  $status  HTTP status code (default is 200).
     * @return void
     */
    function jdd($data, $status = 200): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        response()->json($data, $status)->send();
        exit;
    }
}
