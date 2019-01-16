<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Set trusted proxy IP addresses.
    |--------------------------------------------------------------------------
    |
    | Both IPv4 and IPv6 addresses are supported, along with CIDR notation.
    | The "*" character is syntactic sugar within TrustedProxy to trust any
    | proxy that connects directly to your server, a requirement when you
    | cannot know the address of your proxy (e.g. if using ELB or similar).
    | To trust one or more specific proxies that connect directly to your
    | server, use an array or a string separated by comma of IP addresses.
    | Or, to trust all proxies that connect directly to your server, use a "*".
    |
    | Supported: "*", [<ip addresses>,], "<ip addresses>,"
    |
    */

    'proxies' => env('TRUSTEDPROXY_PROXIES', null),

    /*
    |--------------------------------------------------------------------------
    | Set Headers.
    |--------------------------------------------------------------------------
    |
    | Which headers to use to detect proxy related data (For, Host, Proto, Port).
    | @link https://symfony.com/doc/current/deployment/proxies.html
    |
    | Options include:
    | - Use all x-forwarded-* headers to establish trust:
    |   - \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL
    |   - 'HEADER_X_FORWARDED_ALL'
    | - Use the FORWARDED header to establish trust:
    |   - \Illuminate\Http\Request::HEADER_FORWARDED
    |   - 'HEADER_FORWARDED'
    | - If you are using AWS Elastic Load Balancer:
    |   - \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
    |   - 'HEADER_X_FORWARDED_AWS_ELB'
    |
    */

    'headers' => env('TRUSTEDPROXY_HEADERS', 'HEADER_X_FORWARDED_ALL'),

];
