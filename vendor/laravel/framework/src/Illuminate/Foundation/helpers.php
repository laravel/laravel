<?php

use Carbon\CarbonInterface;
use Illuminate\Broadcasting\FakePendingBroadcast;
use Illuminate\Broadcasting\PendingBroadcast;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cookie\Factory as CookieFactory;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Cookie\CookieJar;
use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Mix;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Log\Context\Repository as ContextRepository;
use Illuminate\Log\LogManager;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Support\Defer\DeferredCallback;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Uri;
use League\Uri\Contracts\UriInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

use function Illuminate\Support\enum_value;

if (! function_exists('abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @return never
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    function abort($code, $message = '', array $headers = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } elseif ($code instanceof Responsable) {
            throw new HttpResponseException($code->toResponse(request()));
        }

        app()->abort($code, $message, $headers);
    }
}

if (! function_exists('abort_if')) {
    /**
     * Throw an HttpException with the given data if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function abort_if($boolean, $code, $message = '', array $headers = []): void
    {
        if ($boolean) {
            abort($code, $message, $headers);
        }
    }
}

if (! function_exists('abort_unless')) {
    /**
     * Throw an HttpException with the given data unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function abort_unless($boolean, $code, $message = '', array $headers = []): void
    {
        if (! $boolean) {
            abort($code, $message, $headers);
        }
    }
}

if (! function_exists('action')) {
    /**
     * Generate the URL to a controller action.
     *
     * @param  string|array  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    function action($name, $parameters = [], $absolute = true): string
    {
        return app('url')->action($name, $parameters, $absolute);
    }
}

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>|null  $abstract
     * @return ($abstract is class-string<TClass> ? TClass : ($abstract is null ? \Illuminate\Foundation\Application : mixed))
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     */
    function app_path($path = ''): string
    {
        return app()->path($path);
    }
}

if (! function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     */
    function asset($path, $secure = null): string
    {
        return app('url')->asset($path, $secure);
    }
}

if (! function_exists('auth')) {
    /**
     * Get the available auth instance.
     *
     * @param  string|null  $guard
     * @return ($guard is null ? \Illuminate\Contracts\Auth\Factory : \Illuminate\Contracts\Auth\Guard)
     */
    function auth($guard = null): AuthFactory|Guard
    {
        if (is_null($guard)) {
            return app(AuthFactory::class);
        }

        return app(AuthFactory::class)->guard($guard);
    }
}

if (! function_exists('back')) {
    /**
     * Create a new redirect response to the previous location.
     *
     * @param  int  $status
     * @param  array  $headers
     * @param  mixed  $fallback
     */
    function back($status = 302, $headers = [], $fallback = false): RedirectResponse
    {
        return app('redirect')->back($status, $headers, $fallback);
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string  $path
     */
    function base_path($path = ''): string
    {
        return app()->basePath($path);
    }
}

if (! function_exists('bcrypt')) {
    /**
     * Hash the given value against the bcrypt algorithm.
     *
     * @param  string  $value
     * @param  array  $options
     */
    function bcrypt($value, $options = []): string
    {
        return app('hash')->driver('bcrypt')->make($value, $options);
    }
}

if (! function_exists('broadcast')) {
    /**
     * Begin broadcasting an event.
     *
     * @param  mixed  $event
     */
    function broadcast($event = null): PendingBroadcast
    {
        return app(BroadcastFactory::class)->event($event);
    }
}

if (! function_exists('broadcast_if')) {
    /**
     * Begin broadcasting an event if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  mixed  $event
     */
    function broadcast_if($boolean, $event = null): PendingBroadcast
    {
        if ($boolean) {
            return app(BroadcastFactory::class)->event(value($event));
        } else {
            return new FakePendingBroadcast;
        }
    }
}

if (! function_exists('broadcast_unless')) {
    /**
     * Begin broadcasting an event unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  mixed  $event
     */
    function broadcast_unless($boolean, $event = null): PendingBroadcast
    {
        if (! $boolean) {
            return app(BroadcastFactory::class)->event(value($event));
        } else {
            return new FakePendingBroadcast;
        }
    }
}

if (! function_exists('cache')) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param  string|array<string, mixed>|null  $key  key|data
     * @param  mixed  $default  default|expiration|null
     * @return ($key is null ? \Illuminate\Cache\CacheManager : ($key is string ? mixed : bool))
     *
     * @throws \InvalidArgumentException
     */
    function cache($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('cache');
        }

        if (is_string($key)) {
            return app('cache')->get($key, $default);
        }

        if (! is_array($key)) {
            throw new InvalidArgumentException(
                'When setting a value in the cache, you must pass an array of key / value pairs.'
            );
        }

        return app('cache')->put(key($key), array_first($key), ttl: $default);
    }
}

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array<string, mixed>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \Illuminate\Config\Repository : ($key is string ? mixed : null))
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
}

if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $path
     */
    function config_path($path = ''): string
    {
        return app()->configPath($path);
    }
}

if (! function_exists('context')) {
    /**
     * Get / set the specified context value.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return ($key is string ? mixed : \Illuminate\Log\Context\Repository)
     */
    function context($key = null, $default = null)
    {
        $context = app(ContextRepository::class);

        return match (true) {
            is_null($key) => $context,
            is_array($key) => $context->add($key),
            default => $context->get($key, $default),
        };
    }
}

if (! function_exists('cookie')) {
    /**
     * Create a new cookie instance.
     *
     * @param  string|null  $name
     * @param  string|null  $value
     * @param  int  $minutes
     * @param  string|null  $path
     * @param  string|null  $domain
     * @param  bool|null  $secure
     * @param  bool  $httpOnly
     * @param  bool  $raw
     * @param  string|null  $sameSite
     * @return ($name is null ? \Illuminate\Cookie\CookieJar : \Symfony\Component\HttpFoundation\Cookie)
     */
    function cookie($name = null, $value = null, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null): CookieJar|Cookie
    {
        $cookie = app(CookieFactory::class);

        if (is_null($name)) {
            return $cookie;
        }

        return $cookie->make($name, $value, $minutes, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }
}

if (! function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     */
    function csrf_field(): HtmlString
    {
        return new HtmlString('<input type="hidden" name="_token" value="'.csrf_token().'" autocomplete="off">');
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @throws \RuntimeException
     */
    function csrf_token(): ?string
    {
        $session = app('session');

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException('Application session store not set.');
    }
}

if (! function_exists('database_path')) {
    /**
     * Get the database path.
     *
     * @param  string  $path
     */
    function database_path($path = ''): string
    {
        return app()->databasePath($path);
    }
}

if (! function_exists('decrypt')) {
    /**
     * Decrypt the given value.
     *
     * @param  string  $value
     * @param  bool  $unserialize
     * @return mixed
     */
    function decrypt($value, $unserialize = true)
    {
        return app('encrypter')->decrypt($value, $unserialize);
    }
}

if (! function_exists('defer')) {
    /**
     * Defer execution of the given callback.
     *
     * @return ($callback is null ? \Illuminate\Support\Defer\DeferredCallbackCollection : \Illuminate\Support\Defer\DeferredCallback)
     */
    function defer(?callable $callback = null, ?string $name = null, bool $always = false): DeferredCallback|DeferredCallbackCollection
    {
        return \Illuminate\Support\defer($callback, $name, $always);
    }
}

if (! function_exists('dispatch')) {
    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param  mixed  $job
     * @return ($job is \Closure ? \Illuminate\Foundation\Bus\PendingClosureDispatch : \Illuminate\Foundation\Bus\PendingDispatch)
     */
    function dispatch($job): PendingDispatch|PendingClosureDispatch
    {
        return $job instanceof Closure
            ? new PendingClosureDispatch(CallQueuedClosure::create($job))
            : new PendingDispatch($job);
    }
}

if (! function_exists('dispatch_sync')) {
    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param  mixed  $job
     * @param  mixed  $handler
     * @return mixed
     */
    function dispatch_sync($job, $handler = null)
    {
        return app(Dispatcher::class)->dispatchSync($job, $handler);
    }
}

if (! function_exists('encrypt')) {
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     */
    function encrypt($value, $serialize = true): string
    {
        return app('encrypter')->encrypt($value, $serialize);
    }
}

if (! function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    function event(...$args)
    {
        return app('events')->dispatch(...$args);
    }
}

if (! function_exists('fake') && class_exists(\Faker\Factory::class)) {
    /**
     * Get a faker instance.
     *
     * @param  string|null  $locale
     */
    function fake($locale = null): \Faker\Generator
    {
        if (app()->bound('config')) {
            $locale ??= app('config')->get('app.faker_locale');
        }

        $locale ??= 'en_US';

        $abstract = \Faker\Generator::class.':'.$locale;

        if (! app()->bound($abstract)) {
            app()->singleton($abstract, fn () => \Faker\Factory::create($locale));
        }

        return app()->make($abstract);
    }
}

if (! function_exists('info')) {
    /**
     * Write some information to the log.
     *
     * @param  string  $message
     * @param  array  $context
     */
    function info($message, $context = []): void
    {
        app('log')->info($message, $context);
    }
}

if (! function_exists('lang_path')) {
    /**
     * Get the path to the language folder.
     *
     * @param  string  $path
     */
    function lang_path($path = ''): string
    {
        return app()->langPath($path);
    }
}

if (! function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param  string|null  $message
     * @return ($message is null ? \Psr\Log\LoggerInterface : null)
     */
    function logger($message = null, array $context = []): ?LoggerInterface
    {
        if (is_null($message)) {
            return app('log');
        }

        return app('log')->debug($message, $context);
    }
}

if (! function_exists('logs')) {
    /**
     * Get a log driver instance.
     *
     * @param  string|null  $driver
     * @return ($driver is null ? \Illuminate\Log\LogManager : \Psr\Log\LoggerInterface)
     */
    function logs($driver = null): LoggerInterface|LogManager
    {
        return $driver ? app('log')->driver($driver) : app('log');
    }
}

if (! function_exists('method_field')) {
    /**
     * Generate a form field to spoof the HTTP verb used by forms.
     *
     * @param  string  $method
     */
    function method_field($method): HtmlString
    {
        return new HtmlString('<input type="hidden" name="_method" value="'.$method.'">');
    }
}

if (! function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     *
     * @throws \Exception
     */
    function mix($path, $manifestDirectory = ''): HtmlString|string
    {
        return app(Mix::class)(...func_get_args());
    }
}

if (! function_exists('now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param  \DateTimeZone|\UnitEnum|string|null  $tz
     */
    function now($tz = null): CarbonInterface
    {
        return Date::now(enum_value($tz));
    }
}

if (! function_exists('old')) {
    /**
     * Retrieve an old input item.
     *
     * @param  string|null  $key
     * @param  \Illuminate\Database\Eloquent\Model|string|array|null  $default
     * @return string|array|null
     */
    function old($key = null, $default = null)
    {
        return app('request')->old($key, $default);
    }
}

if (! function_exists('policy')) {
    /**
     * Get a policy instance for a given class.
     *
     * @param  object|string  $class
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    function policy($class)
    {
        return app(Gate::class)->getPolicyFor($class);
    }
}

if (! function_exists('precognitive')) {
    /**
     * Handle a Precognition controller hook.
     *
     * @param  null|callable  $callable
     * @return mixed
     */
    function precognitive($callable = null)
    {
        $callable ??= function () {
            //
        };

        $payload = $callable(function ($default, $precognition = null) {
            $response = request()->isPrecognitive()
                ? ($precognition ?? $default)
                : $default;

            abort(Router::toResponse(request(), value($response)));
        });

        if (request()->isPrecognitive()) {
            abort(204, headers: ['Precognition-Success' => 'true']);
        }

        return $payload;
    }
}

if (! function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     */
    function public_path($path = ''): string
    {
        return app()->publicPath($path);
    }
}

if (! function_exists('redirect')) {
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null  $to
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return ($to is null ? \Illuminate\Routing\Redirector : \Illuminate\Http\RedirectResponse)
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null): Redirector|RedirectResponse
    {
        if (is_null($to)) {
            return app('redirect');
        }

        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (! function_exists('report')) {
    /**
     * Report an exception.
     *
     * @param  \Throwable|string  $exception
     */
    function report($exception): void
    {
        if (is_string($exception)) {
            $exception = new Exception($exception);
        }

        app(ExceptionHandler::class)->report($exception);
    }
}

if (! function_exists('report_if')) {
    /**
     * Report an exception if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Throwable|string  $exception
     */
    function report_if($boolean, $exception): void
    {
        if ($boolean) {
            report($exception);
        }
    }
}

if (! function_exists('report_unless')) {
    /**
     * Report an exception unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Throwable|string  $exception
     */
    function report_unless($boolean, $exception): void
    {
        if (! $boolean) {
            report($exception);
        }
    }
}

if (! function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  list<string>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \Illuminate\Http\Request : ($key is string ? mixed : array<string, mixed>))
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);

        return is_null($value) ? value($default) : $value;
    }
}

if (! function_exists('rescue')) {
    /**
     * Catch a potential exception and return a default value.
     *
     * @template TValue
     * @template TFallback
     *
     * @param  callable(): TValue  $callback
     * @param  (callable(\Throwable): TFallback)|TFallback  $rescue
     * @param  bool|callable(\Throwable): bool  $report
     * @return TValue|TFallback
     */
    function rescue(callable $callback, $rescue = null, $report = true)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            if (value($report, $e)) {
                report($e);
            }

            return value($rescue, $e);
        }
    }
}

if (! function_exists('resolve')) {
    /**
     * Resolve a service from the container.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $name
     * @return ($name is class-string<TClass> ? TClass : mixed)
     */
    function resolve($name, array $parameters = [])
    {
        return app($name, $parameters);
    }
}

if (! function_exists('resource_path')) {
    /**
     * Get the path to the resources folder.
     *
     * @param  string  $path
     */
    function resource_path($path = ''): string
    {
        return app()->resourcePath($path);
    }
}

if (! function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  \Illuminate\Contracts\View\View|string|array|null  $content
     * @param  int  $status
     * @return ($content is null ? \Illuminate\Contracts\Routing\ResponseFactory : \Illuminate\Http\Response)
     */
    function response($content = null, $status = 200, array $headers = []): ResponseFactory|IlluminateResponse
    {
        $factory = app(ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content ?? '', $status, $headers);
    }
}

if (! function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    function route($name, $parameters = [], $absolute = true): string
    {
        return app('url')->route($name, $parameters, $absolute);
    }
}

if (! function_exists('secure_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     */
    function secure_asset($path): string
    {
        return asset($path, true);
    }
}

if (! function_exists('secure_url')) {
    /**
     * Generate a HTTPS url for the application.
     *
     * @param  string  $path
     * @param  mixed  $parameters
     * @return string
     */
    function secure_url($path, $parameters = [])
    {
        return url($path, $parameters, true);
    }
}

if (! function_exists('session')) {
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array<string, mixed>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \Illuminate\Session\SessionManager : ($key is string ? mixed : null))
     */
    function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('session');
        }

        if (is_array($key)) {
            return app('session')->put($key);
        }

        return app('session')->get($key, $default);
    }
}

if (! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string  $path
     */
    function storage_path($path = ''): string
    {
        return app()->storagePath($path);
    }
}

if (! function_exists('to_action')) {
    /**
     * Create a new redirect response to a controller action.
     *
     * @param  string|array  $action
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    function to_action($action, $parameters = [], $status = 302, $headers = [])
    {
        return redirect()->action($action, $parameters, $status, $headers);
    }
}

if (! function_exists('to_route')) {
    /**
     * Create a new redirect response to a named route.
     *
     * @param  \BackedEnum|string  $route
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    function to_route($route, $parameters = [], $status = 302, $headers = [])
    {
        return redirect()->route($route, $parameters, $status, $headers);
    }
}

if (! function_exists('today')) {
    /**
     * Create a new Carbon instance for the current date.
     *
     * @param  \DateTimeZone|\UnitEnum|string|null  $tz
     * @return \Illuminate\Support\Carbon
     */
    function today($tz = null): CarbonInterface
    {
        return Date::today(enum_value($tz));
    }
}

if (! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return ($key is null ? \Illuminate\Contracts\Translation\Translator : array|string)
     */
    function trans($key = null, $replace = [], $locale = null): Translator|array|string
    {
        if (is_null($key)) {
            return app('translator');
        }

        return app('translator')->get($key, $replace, $locale);
    }
}

if (! function_exists('trans_choice')) {
    /**
     * Translates the given message based on a count.
     *
     * @param  string  $key
     * @param  \Countable|int|float|array  $number
     * @param  string|null  $locale
     */
    function trans_choice($key, $number, array $replace = [], $locale = null): string
    {
        return app('translator')->choice($key, $number, $replace, $locale);
    }
}

if (! function_exists('__')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     */
    function __($key = null, $replace = [], $locale = null): string|array|null
    {
        if (is_null($key)) {
            return $key;
        }

        return trans($key, $replace, $locale);
    }
}

if (! function_exists('uri')) {
    /**
     * Generate a URI for the application.
     */
    function uri(UriInterface|Stringable|array|string $uri, mixed $parameters = [], bool $absolute = true): Uri
    {
        return match (true) {
            is_array($uri) || str_contains($uri, '\\') => Uri::action($uri, $parameters, $absolute),
            str_contains($uri, '.') && Route::has($uri) => Uri::route($uri, $parameters, $absolute),
            default => Uri::of($uri),
        };
    }
}

if (! function_exists('url')) {
    /**
     * Generate a URL for the application.
     *
     * @param  string|null  $path
     * @param  mixed  $parameters
     * @param  bool|null  $secure
     * @return ($path is null ? \Illuminate\Contracts\Routing\UrlGenerator : string)
     */
    function url($path = null, $parameters = [], $secure = null): UrlGenerator|string
    {
        if (is_null($path)) {
            return app(UrlGenerator::class);
        }

        return app(UrlGenerator::class)->to($path, $parameters, $secure);
    }
}

if (! function_exists('validator')) {
    /**
     * Create a new Validator instance.
     *
     * @return ($data is null ? \Illuminate\Contracts\Validation\Factory : \Illuminate\Contracts\Validation\Validator)
     */
    function validator(?array $data = null, array $rules = [], array $messages = [], array $attributes = []): ValidatorContract|ValidationFactory
    {
        $factory = app(ValidationFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data ?? [], $rules, $messages, $attributes);
    }
}

if (! function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return ($view is null ? \Illuminate\Contracts\View\Factory : \Illuminate\Contracts\View\View)
     */
    function view($view = null, $data = [], $mergeData = []): ViewFactory|ViewContract
    {
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}
