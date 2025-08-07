<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="0; url={{ route('install') }}">
        <title>Redirecting to Installer...</title>
    </head>
    <body>
        <p>Redirecting to installer...</p>
    </body>
</html>
