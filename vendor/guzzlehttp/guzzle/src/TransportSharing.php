<?php

namespace GuzzleHttp;

final class TransportSharing
{
    public const NONE = 'none';
    public const HANDLER_PREFER = 'handler_prefer';
    public const HANDLER_REQUIRE = 'handler_require';

    private function __construct()
    {
    }
}
