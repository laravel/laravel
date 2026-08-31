<?php

namespace Illuminate\Http\Client;

use Closure;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as Psr7Response;
use GuzzleHttp\TransferStats;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * @mixin \Illuminate\Http\Client\PendingRequest
 */
class Factory
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The event dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $dispatcher;

    /**
     * The middleware to apply to every request.
     *
     * @var array
     */
    protected $globalMiddleware = [];

    /**
     * The options to apply to every request.
     *
     * @var \Closure|array
     */
    protected $globalOptions = [];

    /**
     * The stub callables that will handle requests.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $stubCallbacks;

    /**
     * Indicates if the factory is recording requests and responses.
     *
     * @var bool
     */
    protected $recording = false;

    /**
     * The recorded response array.
     *
     * @var list<array{0: \Illuminate\Http\Client\Request, 1: \Illuminate\Http\Client\Response|null}>
     */
    protected $recorded = [];

    /**
     * All created response sequences.
     *
     * @var list<\Illuminate\Http\Client\ResponseSequence>
     */
    protected $responseSequences = [];

    /**
     * Indicates that an exception should be thrown if any request is not faked.
     *
     * @var bool
     */
    protected $preventStrayRequests = false;

    /**
     * A list of URL patterns that are allowed to bypass the stray request guard.
     *
     * @var array<int, string>
     */
    protected $allowedStrayRequestUrls = [];

    /**
     * Create a new factory instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $dispatcher
     */
    public function __construct(?Dispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;

        $this->stubCallbacks = new Collection;
    }

    /**
     * Add middleware to apply to every request.
     *
     * @param  callable  $middleware
     * @return $this
     */
    public function globalMiddleware($middleware)
    {
        $this->globalMiddleware[] = $middleware;

        return $this;
    }

    /**
     * Add request middleware to apply to every request.
     *
     * @param  callable  $middleware
     * @return $this
     */
    public function globalRequestMiddleware($middleware)
    {
        $this->globalMiddleware[] = Middleware::mapRequest($middleware);

        return $this;
    }

    /**
     * Add response middleware to apply to every request.
     *
     * @param  callable  $middleware
     * @return $this
     */
    public function globalResponseMiddleware($middleware)
    {
        $this->globalMiddleware[] = Middleware::mapResponse($middleware);

        return $this;
    }

    /**
     * Set the options to apply to every request.
     *
     * @param  \Closure|array  $options
     * @return $this
     */
    public function globalOptions($options)
    {
        $this->globalOptions = $options;

        return $this;
    }

    /**
     * Create a new response instance for use during stubbing.
     *
     * @param  array|string|null  $body
     * @param  int  $status
     * @param  array  $headers
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public static function response($body = null, $status = 200, $headers = [])
    {
        return Create::promiseFor(
            static::psr7Response($body, $status, $headers)
        );
    }

    /**
     * Create a new PSR-7 response instance for use during stubbing.
     *
     * @param  array|string|null  $body
     * @param  int  $status
     * @param  array<string, mixed>  $headers
     * @return \GuzzleHttp\Psr7\Response
     */
    public static function psr7Response($body = null, $status = 200, $headers = [])
    {
        if (is_array($body)) {
            $body = json_encode($body);

            $headers['Content-Type'] = 'application/json';
        }

        return new Psr7Response($status, $headers, $body);
    }

    /**
     * Create a new RequestException instance for use during stubbing.
     *
     * @param  array|string|null  $body
     * @param  int  $status
     * @param  array<string, mixed>  $headers
     * @return \Illuminate\Http\Client\RequestException
     */
    public static function failedRequest($body = null, $status = 200, $headers = [])
    {
        return new RequestException(new Response(static::psr7Response($body, $status, $headers)));
    }

    /**
     * Create a new connection exception for use during stubbing.
     *
     * @param  string|null  $message
     * @return \Closure(\Illuminate\Http\Client\Request): \GuzzleHttp\Promise\PromiseInterface
     */
    public static function failedConnection($message = null)
    {
        return function ($request) use ($message) {
            return Create::rejectionFor(new ConnectException(
                $message ?? "cURL error 6: Could not resolve host: {$request->toPsrRequest()->getUri()->getHost()} (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for {$request->toPsrRequest()->getUri()}.",
                $request->toPsrRequest(),
            ));
        };
    }

    /**
     * Get an invokable object that returns a sequence of responses in order for use during stubbing.
     *
     * @param  array  $responses
     * @return \Illuminate\Http\Client\ResponseSequence
     */
    public function sequence(array $responses = [])
    {
        return $this->responseSequences[] = new ResponseSequence($responses);
    }

    /**
     * Register a stub callable that will intercept requests and be able to return stub responses.
     *
     * @param  callable|array<string, mixed>|null  $callback
     * @return $this
     */
    public function fake($callback = null)
    {
        $this->record();

        $this->recorded = [];

        if (is_null($callback)) {
            $callback = function () {
                return static::response();
            };
        }

        if (is_array($callback)) {
            foreach ($callback as $url => $callable) {
                $this->stubUrl($url, $callable);
            }

            return $this;
        }

        $this->stubCallbacks = $this->stubCallbacks->merge(new Collection([
            function ($request, $options) use ($callback) {
                $response = $callback;

                while ($response instanceof Closure) {
                    $response = $response($request, $options);
                }

                if ($response instanceof PromiseInterface) {
                    $options['on_stats'](new TransferStats(
                        $request->toPsrRequest(),
                        $response->wait(),
                    ));
                }

                return $response;
            },
        ]));

        return $this;
    }

    /**
     * Register a response sequence for the given URL pattern.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Client\ResponseSequence
     */
    public function fakeSequence($url = '*')
    {
        return tap($this->sequence(), function ($sequence) use ($url) {
            $this->fake([$url => $sequence]);
        });
    }

    /**
     * Stub the given URL using the given callback.
     *
     * @param  string  $url
     * @param  \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface|callable|int|string|array|\Illuminate\Http\Client\ResponseSequence  $callback
     * @return $this
     */
    public function stubUrl($url, $callback)
    {
        return $this->fake(function ($request, $options) use ($url, $callback) {
            if (! Str::is(Str::start($url, '*'), $request->url())) {
                return;
            }

            if (is_int($callback) && $callback >= 100 && $callback < 600) {
                return static::response(status: $callback);
            }

            if (is_int($callback) || is_string($callback)) {
                return static::response($callback);
            }

            if ($callback instanceof Closure || $callback instanceof ResponseSequence) {
                return $callback($request, $options);
            }

            return $callback;
        });
    }

    /**
     * Indicate that an exception should be thrown if any request is not faked.
     *
     * @param  bool  $prevent
     * @return $this
     */
    public function preventStrayRequests($prevent = true)
    {
        $this->preventStrayRequests = $prevent;

        return $this;
    }

    /**
     * Determine if stray requests are being prevented.
     *
     * @return bool
     */
    public function preventingStrayRequests()
    {
        return $this->preventStrayRequests;
    }

    /**
     * Allow stray, unfaked requests entirely, or optionally allow only specific URLs.
     *
     * @param  array<int, string>|null  $only
     * @return $this
     */
    public function allowStrayRequests(?array $only = null)
    {
        if (is_null($only)) {
            $this->preventStrayRequests(false);

            $this->allowedStrayRequestUrls = [];
        } else {
            $this->allowedStrayRequestUrls = array_values($only);
        }

        return $this;
    }

    /**
     * Begin recording request / response pairs.
     *
     * @return $this
     */
    public function record()
    {
        $this->recording = true;

        return $this;
    }

    /**
     * Record a request response pair.
     *
     * @param  \Illuminate\Http\Client\Request  $request
     * @param  \Illuminate\Http\Client\Response|null  $response
     * @return void
     */
    public function recordRequestResponsePair($request, $response)
    {
        if ($this->recording) {
            $this->recorded[] = [$request, $response];
        }
    }

    /**
     * Assert that a request / response pair was recorded matching a given truth test.
     *
     * @param  callable|(\Closure(\Illuminate\Http\Client\Request, \Illuminate\Http\Client\Response|null): bool)  $callback
     * @return void
     */
    public function assertSent($callback)
    {
        PHPUnit::assertTrue(
            $this->recorded($callback)->count() > 0,
            'An expected request was not recorded.'
        );
    }

    /**
     * Assert that the given request was sent in the given order.
     *
     * @param  list<string|(\Closure(\Illuminate\Http\Client\Request, \Illuminate\Http\Client\Response|null): bool)|callable>  $callbacks
     * @return void
     */
    public function assertSentInOrder($callbacks)
    {
        $this->assertSentCount(count($callbacks));

        foreach ($callbacks as $index => $url) {
            $callback = is_callable($url) ? $url : function ($request) use ($url) {
                return $request->url() == $url;
            };

            PHPUnit::assertTrue($callback(
                $this->recorded[$index][0],
                $this->recorded[$index][1]
            ), 'An expected request (#'.($index + 1).') was not recorded.');
        }
    }

    /**
     * Assert that a request / response pair was not recorded matching a given truth test.
     *
     * @param  callable|(\Closure(\Illuminate\Http\Client\Request, \Illuminate\Http\Client\Response|null): bool)  $callback
     * @return void
     */
    public function assertNotSent($callback)
    {
        PHPUnit::assertFalse(
            $this->recorded($callback)->count() > 0,
            'Unexpected request was recorded.'
        );
    }

    /**
     * Assert that no request / response pair was recorded.
     *
     * @return void
     */
    public function assertNothingSent()
    {
        PHPUnit::assertEmpty(
            $this->recorded,
            'Requests were recorded.'
        );
    }

    /**
     * Assert how many requests have been recorded.
     *
     * @param  int  $count
     * @return void
     */
    public function assertSentCount($count)
    {
        PHPUnit::assertCount($count, $this->recorded);
    }

    /**
     * Assert that every created response sequence is empty.
     *
     * @return void
     */
    public function assertSequencesAreEmpty()
    {
        foreach ($this->responseSequences as $responseSequence) {
            PHPUnit::assertTrue(
                $responseSequence->isEmpty(),
                'Not all response sequences are empty.'
            );
        }
    }

    /**
     * Get a collection of the request / response pairs matching the given truth test.
     *
     * @param  (\Closure(\Illuminate\Http\Client\Request, \Illuminate\Http\Client\Response|null): bool)|callable  $callback
     * @return \Illuminate\Support\Collection<int, array{0: \Illuminate\Http\Client\Request, 1: \Illuminate\Http\Client\Response|null}>
     */
    public function recorded($callback = null)
    {
        if (empty($this->recorded)) {
            return new Collection;
        }

        $collect = new Collection($this->recorded);

        if ($callback) {
            return $collect->filter(fn ($pair) => $callback($pair[0], $pair[1]));
        }

        return $collect;
    }

    /**
     * Create a new pending request instance for this factory.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function createPendingRequest()
    {
        return tap($this->newPendingRequest(), function ($request) {
            $request
                ->stub($this->stubCallbacks)
                ->preventStrayRequests($this->preventStrayRequests)
                ->allowStrayRequests($this->allowedStrayRequestUrls);
        });
    }

    /**
     * Instantiate a new pending request instance for this factory.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function newPendingRequest()
    {
        return (new PendingRequest($this, $this->globalMiddleware))->withOptions(value($this->globalOptions));
    }

    /**
     * Get the current event dispatcher implementation.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher|null
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Get the array of global middleware.
     *
     * @return array
     */
    public function getGlobalMiddleware()
    {
        return $this->globalMiddleware;
    }

    /**
     * Execute a method against a new pending request instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->createPendingRequest()->{$method}(...$parameters);
    }
}
