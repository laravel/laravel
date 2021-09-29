<?php

namespace App\Http;

use App\Http\Middleware\BackofficeRedirectExpiredPassword;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    public const API = 'api';
    public const PUBLIC_API = 'public-api';
    public const WEB = 'web';
    public const BACKOFFICE = 'backoffice';
    public const BACKOFFICE_PUBLIC = 'backoffice-public';
    public const BACKOFFICE_LISTING = 'backoffice-listing';

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\LocaleMiddleware::class,
        \App\Http\Middleware\SentryContext::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        self::WEB => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        self::PUBLIC_API => [
            \App\Http\Middleware\SetApiDefaultHeaders::class,
        ],
        self::API => [
            self::PUBLIC_API,
            'auth:api',
        ],
        self::BACKOFFICE => [
            self::WEB,
            'security:backoffice',
            BackofficeRedirectExpiredPassword::class,
        ],
        self::BACKOFFICE_PUBLIC => [
            self::WEB,
            'security:backoffice:public',
        ],
        self::BACKOFFICE_LISTING => [
            self::WEB,
            self::BACKOFFICE,
            'persistent',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \App\Http\Middleware\SetApiDefaultHeaders::class,
        \App\Http\Middleware\LocaleMiddleware::class,
        \Illuminate\Auth\Middleware\Authenticate::class,
        \Illuminate\Auth\Middleware\Authorize::class,
        \App\Http\Middleware\SentryContext::class,
    ];
}
