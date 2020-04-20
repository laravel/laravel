<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 404: Page Not Found
    |--------------------------------------------------------------------------
    |
    | Normally shows when theres an application error.
    | Must use minimal layout.
    |
    */

    'not_found' => [
        'title' => 'Not Found',
        'message' => 'It looks like the page you’re looking for is no longer here.',
        // 'cta' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | 500: Internal Server Error
    |--------------------------------------------------------------------------
    |
    | Normally shows when theres an application error.
    | Must use minimal layout.
    |
    */

    'server_error' => [
        'title' => 'Server Error',
        'message' => 'It looks like something has broken.',
    ],


    /*
    |--------------------------------------------------------------------------
    | 401: Unauthorized
    |--------------------------------------------------------------------------
    |
    | 401 semantically means "unauthorised", the user does not have valid
    | authentication credentials for the target resource.
    |
    */

    'unauthorised' => [
        'title' => 'Unauthorised',
        'message' => 'You’re unauthorised to make this request.',
    ],


    /*
    |--------------------------------------------------------------------------
    | 403: Forbidden
    |--------------------------------------------------------------------------
    |
    | The request contained valid data and was understood by the server, but the
    | server is refusing action. This may be due to the user not having the
    | necessary permissions for a resource or needing an account of some sort.
    |
    */

    'forbidden' => [
        'title' => 'Forbidden',
        'message' => 'You’re unauthorised to make this request.',
    ],


    /*
    |--------------------------------------------------------------------------
    | 419: Page Expired
    |--------------------------------------------------------------------------
    |
    | Shown when a CSRF Token is missing or expired.
    |
    */

    'page_expired' => [
        'title' => 'Page Expired',
        'message' => 'The page has expired.',
    ],


    /*
    |--------------------------------------------------------------------------
    | 429: Too Many Requests
    |--------------------------------------------------------------------------
    |
    | The user has sent too many requests in a given amount of time.
    | Intended for use with rate-limiting schemes.
    |
    */

    'max_requests' => [
        'title' => 'Too Many Requests',
        'message' => 'There were too many requests to the server.',
        'cta' => false,
    ],
];
