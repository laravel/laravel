<?php

namespace Illuminate\Http\Client;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\UriTemplate\UriTemplate;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Promises\FluentPromise;
use Illuminate\Http\Client\Promises\LazyPromise;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\VarDumper\VarDumper;
use Throwable;

/**
 * @template TAsync of bool = false
 */
class PendingRequest
{
    use Conditionable, Macroable;

    /**
     * The factory instance.
     *
     * @var \Illuminate\Http\Client\Factory|null
     */
    protected $factory;

    /**
     * The Guzzle client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The Guzzle HTTP handler.
     *
     * @var callable
     */
    protected $handler;

    /**
     * The base URL for the request.
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * The parameters that can be substituted into the URL.
     *
     * @var array
     */
    protected $urlParameters = [];

    /**
     * The request body format.
     *
     * @var string
     */
    protected $bodyFormat;

    /**
     * The raw body for the request.
     *
     * @var \Psr\Http\Message\StreamInterface|string
     */
    protected $pendingBody;

    /**
     * The pending files for the request.
     *
     * @var array
     */
    protected $pendingFiles = [];

    /**
     * The request cookies.
     *
     * @var array
     */
    protected $cookies;

    /**
     * The transfer stats for the request.
     *
     * @var \GuzzleHttp\TransferStats
     */
    protected $transferStats;

    /**
     * The request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * A callback to run when throwing if a server or client error occurs.
     *
     * @var \Closure
     */
    protected $throwCallback;

    /**
     * A callback to check if an exception should be thrown when a server or client error occurs.
     *
     * @var \Closure
     */
    protected $throwIfCallback;

    /**
     * The number of times to try the request.
     *
     * @var int
     */
    protected $tries = 1;

    /**
     * The number of milliseconds to wait between retries.
     *
     * @var (Closure(int, mixed): int)|int
     */
    protected $retryDelay = 100;

    /**
     * Whether to throw an exception when all retries fail.
     *
     * @var bool
     */
    protected $retryThrow = true;

    /**
     * The callback that will determine if the request should be retried.
     *
     * @var (callable(\Throwable, static, string|null): bool)|null
     */
    protected $retryWhenCallback = null;

    /**
     * The callbacks that should execute before the request is sent.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $beforeSendingCallbacks;

    /**
     * The callbacks that should execute after the Laravel Response is built.
     *
     * @var \Illuminate\Support\Collection<int, (callable(\Illuminate\Http\Client\Response): \Illuminate\Http\Client\Response|null)>
     */
    protected $afterResponseCallbacks;

    /**
     * The stub callables that will handle requests.
     *
     * @var \Illuminate\Support\Collection|null
     */
    protected $stubCallbacks;

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
     * The middleware callables added by users that will handle requests.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $middleware;

    /**
     * Whether the requests should be asynchronous.
     *
     * @var TAsync
     */
    protected $async = false;

    /**
     * The attributes to track with the request.
     *
     * @var array<array-key, mixed>
     */
    protected $attributes = [];

    /**
     * The pending request promise.
     *
     * @var \GuzzleHttp\Promise\PromiseInterface
     */
    protected $promise;

    /**
     * The sent request object, if a request has been made.
     *
     * @var \Illuminate\Http\Client\Request|null
     */
    protected $request;

    /**
     * The Guzzle request options that are mergeable via array_merge_recursive.
     *
     * @var array
     */
    protected $mergeableOptions = [
        'cookies',
        'form_params',
        'headers',
        'json',
        'multipart',
        'query',
    ];

    /**
     * The length at which request exceptions will be truncated.
     *
     * @var int<1, max>|false|null
     */
    protected $truncateExceptionsAt = null;

    /**
     * Create a new HTTP Client instance.
     *
     * @param  \Illuminate\Http\Client\Factory|null  $factory
     * @param  array  $middleware
     */
    public function __construct(?Factory $factory = null, $middleware = [])
    {
        $this->factory = $factory;
        $this->middleware = new Collection($middleware);

        $this->asJson();

        $this->options = [
            'connect_timeout' => 10,
            'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
            'http_errors' => false,
            'timeout' => 30,
        ];

        $this->beforeSendingCallbacks = new Collection([function (Request $request, array $options, PendingRequest $pendingRequest) {
            $pendingRequest->request = $request;
            $pendingRequest->cookies = $options['cookies'];

            $pendingRequest->dispatchRequestSendingEvent();
        }]);

        $this->afterResponseCallbacks = new Collection();
    }

    /**
     * Set the base URL for the pending request.
     *
     * @param  string  $url
     * @return $this
     */
    public function baseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Attach a raw body to the request.
     *
     * @param  \Psr\Http\Message\StreamInterface|string  $content
     * @param  string  $contentType
     * @return $this
     */
    public function withBody($content, $contentType = 'application/json')
    {
        $this->bodyFormat('body');

        $this->pendingBody = $content;

        $this->contentType($contentType);

        return $this;
    }

    /**
     * Indicate the request contains JSON.
     *
     * @return $this
     */
    public function asJson()
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    /**
     * Indicate the request contains form parameters.
     *
     * @return $this
     */
    public function asForm()
    {
        return $this->bodyFormat('form_params')->contentType('application/x-www-form-urlencoded');
    }

    /**
     * Attach a file to the request.
     *
     * @param  string|array  $name
     * @param  string|resource  $contents
     * @param  string|null  $filename
     * @param  array  $headers
     * @return $this
     */
    public function attach($name, $contents = '', $filename = null, array $headers = [])
    {
        if (is_array($name)) {
            foreach ($name as $file) {
                $this->attach(...$file);
            }

            return $this;
        }

        $this->asMultipart();

        $file = [
            'name' => $name,
            'contents' => $contents,
            'headers' => $headers,
        ];

        if ($filename !== null) {
            $file['filename'] = $filename;
        }

        $this->pendingFiles[] = $file;

        return $this;
    }

    /**
     * Indicate the request is a multi-part form request.
     *
     * @return $this
     */
    public function asMultipart()
    {
        return $this->bodyFormat('multipart');
    }

    /**
     * Specify the body format of the request.
     *
     * @param  string  $format
     * @return $this
     */
    public function bodyFormat(string $format)
    {
        $this->bodyFormat = $format;

        return $this;
    }

    /**
     * Set the given query parameters in the request URI.
     *
     * @param  array  $parameters
     * @return $this
     */
    public function withQueryParameters(array $parameters)
    {
        $this->options = array_merge_recursive($this->options, [
            'query' => $parameters,
        ]);

        return $this;
    }

    /**
     * Specify the request's content type.
     *
     * @param  string  $contentType
     * @return $this
     */
    public function contentType(string $contentType)
    {
        $this->options['headers']['Content-Type'] = $contentType;

        return $this;
    }

    /**
     * Indicate that JSON should be returned by the server.
     *
     * @return $this
     */
    public function acceptJson()
    {
        return $this->accept('application/json');
    }

    /**
     * Indicate the type of content that should be returned by the server.
     *
     * @param  string  $contentType
     * @return $this
     */
    public function accept($contentType)
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    /**
     * Add the given headers to the request.
     *
     * @param  array  $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->options = array_merge_recursive($this->options, [
            'headers' => $headers,
        ]);

        return $this;
    }

    /**
     * Add the given header to the request.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return $this
     */
    public function withHeader($name, $value)
    {
        return $this->withHeaders([$name => $value]);
    }

    /**
     * Replace the given headers on the request.
     *
     * @param  array  $headers
     * @return $this
     */
    public function replaceHeaders(array $headers)
    {
        $this->options['headers'] = array_merge($this->options['headers'] ?? [], $headers);

        return $this;
    }

    /**
     * Specify the basic authentication username and password for the request.
     *
     * @param  string  $username
     * @param  string  $password
     * @return $this
     */
    public function withBasicAuth(string $username, string $password)
    {
        $this->options['auth'] = [$username, $password];

        return $this;
    }

    /**
     * Specify the digest authentication username and password for the request.
     *
     * @param  string  $username
     * @param  string  $password
     * @return $this
     */
    public function withDigestAuth($username, $password)
    {
        $this->options['auth'] = [$username, $password, 'digest'];

        return $this;
    }

    /**
     * Specify the NTLM authentication username and password for the request.
     *
     * @param  string  $username
     * @param  string  $password
     * @return $this
     */
    public function withNtlmAuth($username, $password)
    {
        $this->options['auth'] = [$username, $password, 'ntlm'];

        return $this;
    }

    /**
     * Specify an authorization token for the request.
     *
     * @param  string  $token
     * @param  string  $type
     * @return $this
     */
    public function withToken($token, $type = 'Bearer')
    {
        $this->options['headers']['Authorization'] = trim($type.' '.$token);

        return $this;
    }

    /**
     * Specify the user agent for the request.
     *
     * @param  string|bool  $userAgent
     * @return $this
     */
    public function withUserAgent($userAgent)
    {
        $this->options['headers']['User-Agent'] = trim($userAgent);

        return $this;
    }

    /**
     * Specify the URL parameters that can be substituted into the request URL.
     *
     * @param  array  $parameters
     * @return $this
     */
    public function withUrlParameters(array $parameters = [])
    {
        $this->urlParameters = array_merge($this->urlParameters, $parameters);

        return $this;
    }

    /**
     * Specify the cookies that should be included with the request.
     *
     * @param  array  $cookies
     * @param  string  $domain
     * @return $this
     */
    public function withCookies(array $cookies, string $domain)
    {
        $this->options = array_merge_recursive($this->options, [
            'cookies' => CookieJar::fromArray($cookies, $domain),
        ]);

        return $this;
    }

    /**
     * Specify the maximum number of redirects to allow.
     *
     * @param  int  $max
     * @return $this
     */
    public function maxRedirects(int $max)
    {
        $this->options['allow_redirects']['max'] = $max;

        return $this;
    }

    /**
     * Indicate that redirects should not be followed.
     *
     * @return $this
     */
    public function withoutRedirecting()
    {
        $this->options['allow_redirects'] = false;

        return $this;
    }

    /**
     * Indicate that TLS certificates should not be verified.
     *
     * @return $this
     */
    public function withoutVerifying()
    {
        $this->options['verify'] = false;

        return $this;
    }

    /**
     * Specify the path where the body of the response should be stored.
     *
     * @param  string|resource  $to
     * @return $this
     */
    public function sink($to)
    {
        $this->options['sink'] = $to;

        return $this;
    }

    /**
     * Specify the timeout (in seconds) for the request.
     *
     * @param  int|float  $seconds
     * @return $this
     */
    public function timeout(int|float $seconds)
    {
        $this->options['timeout'] = $seconds;

        return $this;
    }

    /**
     * Specify the connect timeout (in seconds) for the request.
     *
     * @param  int|float  $seconds
     * @return $this
     */
    public function connectTimeout(int|float $seconds)
    {
        $this->options['connect_timeout'] = $seconds;

        return $this;
    }

    /**
     * Specify the number of times the request should be attempted.
     *
     * @param  array|int  $times
     * @param  (Closure(int, mixed): int)|int  $sleepMilliseconds
     * @param  (callable(\Throwable, static, string|null): bool)|null  $when
     * @param  bool  $throw
     * @return $this
     */
    public function retry(array|int $times, Closure|int $sleepMilliseconds = 0, ?callable $when = null, bool $throw = true)
    {
        $this->tries = $times;
        $this->retryDelay = $sleepMilliseconds;
        $this->retryWhenCallback = $when;
        $this->retryThrow = $throw;

        return $this;
    }

    /**
     * Replace the specified options on the request.
     *
     * @param  array  $options
     * @return $this
     */
    public function withOptions(array $options)
    {
        $this->options = array_replace_recursive(
            array_merge_recursive($this->options, Arr::only($options, $this->mergeableOptions)),
            $options
        );

        return $this;
    }

    /**
     * Add new middleware the client handler stack.
     *
     * @param  callable  $middleware
     * @return $this
     */
    public function withMiddleware(callable $middleware)
    {
        $this->middleware->push($middleware);

        return $this;
    }

    /**
     * Add new request middleware the client handler stack.
     *
     * @param  callable  $middleware
     * @return $this
     */
    public function withRequestMiddleware(callable $middleware)
    {
        $this->middleware->push(Middleware::mapRequest($middleware));

        return $this;
    }

    /**
     * Add new response middleware the client handler stack.
     *
     * @param  callable  $middleware
     * @return $this
     */
    public function withResponseMiddleware(callable $middleware)
    {
        $this->middleware->push(Middleware::mapResponse($middleware));

        return $this;
    }

    /**
     * Set arbitrary attributes to store with the request.
     *
     * @param  array<array-key, mixed>  $attributes
     * @return $this
     */
    public function withAttributes($attributes)
    {
        $this->attributes = array_merge_recursive($this->attributes, $attributes);

        return $this;
    }

    /**
     * Add a new "before sending" callback to the request.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function beforeSending($callback)
    {
        $this->beforeSendingCallbacks[] = $callback;

        return $this;
    }

    /**
     * Add a new callback to execute after the response is built.
     *
     * @param  (callable(\Illuminate\Http\Client\Response): \Illuminate\Http\Client\Response|null)  $callback
     * @return $this
     */
    public function afterResponse(callable $callback)
    {
        $this->afterResponseCallbacks[] = $callback;

        return $this;
    }

    /**
     * Throw an exception if a server or client error occurs.
     *
     * @param  callable|null  $callback
     * @return $this
     */
    public function throw(?callable $callback = null)
    {
        $this->throwCallback = $callback ?: fn () => null;

        return $this;
    }

    /**
     * Throw an exception if a server or client error occurred and the given condition evaluates to true.
     *
     * @param  callable|bool  $condition
     * @return $this
     */
    public function throwIf($condition)
    {
        if (is_callable($condition)) {
            $this->throwIfCallback = $condition;
        }

        return $condition ? $this->throw(func_get_args()[1] ?? null) : $this;
    }

    /**
     * Throw an exception if a server or client error occurred and the given condition evaluates to false.
     *
     * @param  callable|bool  $condition
     * @return $this
     */
    public function throwUnless($condition)
    {
        return $this->throwIf(! $condition);
    }

    /**
     * Dump the request before sending.
     *
     * @return $this
     */
    public function dump()
    {
        $values = func_get_args();

        return $this->beforeSending(function (Request $request, array $options) use ($values) {
            foreach (array_merge($values, [$request, $options]) as $value) {
                VarDumper::dump($value);
            }
        });
    }

    /**
     * Dump the request before sending and end the script.
     *
     * @return $this
     */
    public function dd()
    {
        $values = func_get_args();

        return $this->beforeSending(function (Request $request, array $options) use ($values) {
            foreach (array_merge($values, [$request, $options]) as $value) {
                VarDumper::dump($value);
            }

            exit(1);
        });
    }

    /**
     * Issue a GET request to the given URL.
     *
     * @param  string  $url
     * @param  array|string|null  $query
     * @return \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
     *
     * @phpstan-return (TAsync is false ?  \Illuminate\Http\Client\Response : \GuzzleHttp\Promise\PromiseInterface)
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function get(string $url, $query = null)
    {
        return $this->send('GET', $url, func_num_args() === 1 ? [] : [
            'query' => $query,
        ]);
    }

    /**
     * Issue a HEAD request to the given URL.
     *
     * @param  string  $url
     * @param  array|string|null  $query
     * @return \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
     *
     * @phpstan-return (TAsync is false ?  \Illuminate\Http\Client\Response : \GuzzleHttp\Promise\PromiseInterface)
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function head(string $url, $query = null)
    {
        return $this->send('HEAD', $url, func_num_args() === 1 ? [] : [
            'query' => $query,
        ]);
    }

    /**
     * Issue a POST request to the given URL.
     *
     * @param  string  $url
     * @param  array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable  $data
     * @return \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
     *
     * @phpstan-return (TAsync is false ?  \Illuminate\Http\Client\Response : \GuzzleHttp\Promise\PromiseInterface)
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function post(string $url, $data = [])
    {
        return $this->send('POST', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a PATCH request to the given URL.
     *
     * @param  string  $url
     * @param  array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable  $data
     * @return \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
     *
     * @phpstan-return (TAsync is false ?  \Illuminate\Http\Client\Response : \GuzzleHttp\Promise\PromiseInterface)
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function patch(string $url, $data = [])
    {
        return $this->send('PATCH', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a PUT request to the given URL.
     *
     * @param  string  $url
     * @param  array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable  $data
     * @return \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
     *
     * @phpstan-return (TAsync is false ?  \Illuminate\Http\Client\Response : \GuzzleHttp\Promise\PromiseInterface)
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function put(string $url, $data = [])
    {
        return $this->send('PUT', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a DELETE request to the given URL.
     *
     * @param  string  $url
     * @param  array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable  $data
     * @return \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
     *
     * @phpstan-return (TAsync is false ?  \Illuminate\Http\Client\Response : \GuzzleHttp\Promise\PromiseInterface)
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function delete(string $url, $data = [])
    {
        return $this->send('DELETE', $url, empty($data) ? [] : [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Send a pool of asynchronous requests concurrently.
     *
     * @param  (callable(\Illuminate\Http\Client\Pool): mixed)  $callback
     * @param  non-negative-int|null  $concurrency
     * @return array<array-key, \Illuminate\Http\Client\Response|\Throwable>
     */
    public function pool(callable $callback, ?int $concurrency = null)
    {
        $results = [];

        $requests = tap(new Pool($this->factory), $callback)->getRequests();

        if ($concurrency === null) {
            (new Collection($requests))->each(static function ($item) {
                if ($item instanceof static) {
                    $item = $item->getPromise();
                }

                if ($item instanceof LazyPromise) {
                    $item->buildPromise();
                }
            });

            foreach ($requests as $key => $item) {
                $results[$key] = $item instanceof static ? $item->getPromise()->wait() : $item->wait();
            }

            return $results;
        }

        $concurrency = $concurrency === 0 ? count($requests) : $concurrency;

        $promiseGenerator = static function () use ($requests) {
            foreach ($requests as $key => $item) {
                $promise = $item instanceof static ? $item->getPromise() : $item;
                yield $key => $promise instanceof LazyPromise ? $promise->buildPromise() : $promise;
            }
        };

        (new EachPromise($promiseGenerator(), [
            'fulfilled' => function ($result, $key) use (&$results) {
                $results[$key] = $result;
            },
            'rejected' => function ($reason, $key) use (&$results) {
                $results[$key] = $reason;
            },
            'concurrency' => $concurrency,
        ]))->promise()->wait();

        return $results;
    }

    /**
     * Send a pool of asynchronous requests concurrently, with callbacks for introspection.
     *
     * @param  callable  $callback
     * @return \Illuminate\Http\Client\Batch
     */
    public function batch(callable $callback): Batch
    {
        return tap(new Batch($this->factory), $callback);
    }

    /**
     * Send the request to the given URL.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @return \Illuminate\Http\Client\Response|\Illuminate\Http\Client\Promises\LazyPromise
     *
     * @phpstan-return (TAsync is false ? \Illuminate\Http\Client\Response : \Illuminate\Http\Client\Promises\LazyPromise)
     *
     * @throws \Exception
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function send(string $method, string $url, array $options = [])
    {
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            $url = ltrim(rtrim($this->baseUrl, '/').'/'.ltrim($url, '/'), '/');
        }

        $url = $this->expandUrlParameters($url);

        $options = $this->parseHttpOptions($options);

        [$this->pendingBody, $this->pendingFiles] = [null, []];

        if ($this->async) {
            return $this->promise = new LazyPromise(
                fn () => $this->makePromise($method, $url, $options)
            );
        }

        $shouldRetry = null;

        return retry($this->tries ?? 1, function ($attempt) use ($method, $url, $options, &$shouldRetry) {
            try {
                return tap($this->newResponse($this->sendRequest($method, $url, $options)), function (&$response) use ($attempt, &$shouldRetry) {
                    $this->populateResponse($response);

                    $this->dispatchResponseReceivedEvent($response);
                    $response = $this->runAfterResponseCallbacks($response);

                    if ($response->successful()) {
                        return;
                    }

                    try {
                        $shouldRetry = $this->retryWhenCallback ? call_user_func($this->retryWhenCallback, $response->toException(), $this, $this->request->toPsrRequest()->getMethod()) : true;
                    } catch (Exception $exception) {
                        $shouldRetry = false;

                        throw $exception;
                    }

                    if ($this->throwCallback &&
                        ($this->throwIfCallback === null ||
                         call_user_func($this->throwIfCallback, $response))) {
                        $response->throw($this->throwCallback);
                    }

                    $potentialTries = is_array($this->tries)
                        ? count($this->tries) + 1
                        : $this->tries;

                    if ($attempt < $potentialTries && $shouldRetry) {
                        $response->throw();
                    }

                    if ($potentialTries > 1 && $this->retryThrow) {
                        $response->throw();
                    }
                });
            } catch (TransferException $e) {
                if ($e instanceof ConnectException) {
                    $this->marshalConnectionException($e);
                }

                if ($e instanceof RequestException && ! $e->hasResponse()) {
                    $this->marshalRequestExceptionWithoutResponse($e);
                }

                if ($e instanceof RequestException && $e->hasResponse()) {
                    $this->marshalRequestExceptionWithResponse($e);
                }

                throw $e;
            }
        }, $this->retryDelay ?? 100, function ($exception) use (&$shouldRetry) {
            $result = $shouldRetry !== null ? $shouldRetry : ($this->retryWhenCallback ? call_user_func($this->retryWhenCallback, $exception, $this, $this->request?->toPsrRequest()->getMethod()) : true);

            $shouldRetry = null;

            return $result;
        });
    }

    /**
     * Substitute the URL parameters in the given URL.
     *
     * @param  string  $url
     * @return string
     */
    protected function expandUrlParameters(string $url)
    {
        return UriTemplate::expand($url, $this->urlParameters);
    }

    /**
     * Parse the given HTTP options and set the appropriate additional options.
     *
     * @param  array  $options
     * @return array
     */
    protected function parseHttpOptions(array $options)
    {
        if (isset($options[$this->bodyFormat])) {
            if ($this->bodyFormat === 'multipart') {
                $options[$this->bodyFormat] = $this->parseMultipartBodyFormat($options[$this->bodyFormat]);
            } elseif ($this->bodyFormat === 'body') {
                $options[$this->bodyFormat] = $this->pendingBody;
            }

            if (is_array($options[$this->bodyFormat])) {
                $options[$this->bodyFormat] = array_merge(
                    $options[$this->bodyFormat], $this->pendingFiles
                );
            }
        } else {
            $options[$this->bodyFormat] = $this->pendingBody;
        }

        return (new Collection($options))
            ->map(function ($value, $key) {
                if ($key === 'json' && $value instanceof JsonSerializable) {
                    return $value;
                }

                return $value instanceof Arrayable ? $value->toArray() : $value;
            })
            ->all();
    }

    /**
     * Parse multi-part form data.
     *
     * @param  array  $data
     * @return array|array[]
     */
    protected function parseMultipartBodyFormat(array $data)
    {
        return (new Collection($data))
            ->flatMap(function ($value, $key) {
                if (is_array($value)) {
                    // If the array has 'name' and 'contents' keys, it's already formatted for multipart...
                    if (isset($value['name']) && isset($value['contents'])) {
                        return [$value];
                    }

                    // Otherwise, treat it as multiple values for the same field name...
                    return (new Collection($value))->map(function ($item) use ($key) {
                        return ['name' => $key.'[]', 'contents' => $item];
                    });
                }

                return [['name' => $key, 'contents' => $value]];
            })
            ->values()
            ->all();
    }

    /**
     * Send an asynchronous request to the given URL.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @param  int  $attempt
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function makePromise(string $method, string $url, array $options = [], int $attempt = 1)
    {
        return $this->promise = $this->sendRequest($method, $url, $options)
            ->then(function (MessageInterface $message) {
                $response = $this->newResponse($message);

                $this->populateResponse($response);
                $this->dispatchResponseReceivedEvent($response);

                return $this->runAfterResponseCallbacks($response);
            })
            ->otherwise(function (Throwable $e) {
                if ($e instanceof StrayRequestException) {
                    throw $e;
                }

                if ($e instanceof ConnectException || ($e instanceof RequestException && ! $e->hasResponse())) {
                    $exception = new ConnectionException($e->getMessage(), 0, $e);

                    $this->dispatchConnectionFailedEvent(
                        (new Request($e->getRequest()))->setRequestAttributes($this->attributes),
                        $exception
                    );

                    return $exception;
                }

                return $e instanceof RequestException && $e->hasResponse() ? $this->populateResponse($this->newResponse($e->getResponse())) : $e;
            })
            ->then(function (Response|Throwable $response) use ($method, $url, $options, $attempt) {
                return $this->handlePromiseResponse($response, $method, $url, $options, $attempt);
            });
    }

    /**
     * Handle the response of an asynchronous request.
     *
     * @param  \Illuminate\Http\Client\Response|\Throwable  $response
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @param  int  $attempt
     * @return mixed
     */
    protected function handlePromiseResponse(Response|Throwable $response, $method, $url, $options, $attempt)
    {
        if ($response instanceof Response && $response->successful()) {
            return $response;
        }

        if ($response instanceof RequestException) {
            $response = $this->populateResponse($this->newResponse($response->getResponse()));
        }

        try {
            $shouldRetry = $this->retryWhenCallback ? call_user_func(
                $this->retryWhenCallback,
                $response instanceof Response ? $response->toException() : $response,
                $this
            ) : true;
        } catch (Exception $exception) {
            return $exception;
        }

        if ($attempt < $this->tries && $shouldRetry) {
            $options['delay'] = value(
                $this->retryDelay,
                $attempt,
                $response instanceof Response ? $response->toException() : $response
            );

            return $this->makePromise($method, $url, $options, $attempt + 1);
        }

        if ($response instanceof Response &&
            $this->throwCallback &&
            ($this->throwIfCallback === null || call_user_func($this->throwIfCallback, $response))) {
            try {
                $response->throw($this->throwCallback);
            } catch (Exception $exception) {
                return $exception;
            }
        }

        if ($this->tries > 1 && $this->retryThrow) {
            return $response instanceof Response ? $response->toException() : $response;
        }

        return $response;
    }

    /**
     * Send a request either synchronously or asynchronously.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @return \Psr\Http\Message\MessageInterface|\GuzzleHttp\Promise\PromiseInterface
     *
     * @throws \Exception
     */
    protected function sendRequest(string $method, string $url, array $options = [])
    {
        $clientMethod = $this->async ? 'requestAsync' : 'request';

        $laravelData = $this->parseRequestData($method, $url, $options);

        $onStats = function ($transferStats) {
            if (($callback = ($this->options['on_stats'] ?? false)) instanceof Closure) {
                $transferStats = $callback($transferStats) ?: $transferStats;
            }

            $this->transferStats = $transferStats;
        };

        $mergedOptions = $this->normalizeRequestOptions($this->mergeOptions([
            'laravel_data' => $laravelData,
            'on_stats' => $onStats,
        ], $options));

        $result = $this->buildClient()->$clientMethod($method, $url, $mergedOptions);

        if ($result instanceof PromiseInterface && ! $result instanceof FluentPromise) {
            $result = new FluentPromise($result);
        }

        return $result;
    }

    /**
     * Get the request data as an array so that we can attach it to the request for convenient assertions.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @return array
     */
    protected function parseRequestData($method, $url, array $options)
    {
        if ($this->bodyFormat === 'body') {
            return [];
        }

        $laravelData = $options[$this->bodyFormat] ?? $options['query'] ?? [];

        $urlString = (new Stringable($url));

        if (empty($laravelData) && $method === 'GET' && $urlString->contains('?')) {
            $laravelData = (string) $urlString->after('?');
        }

        if (is_string($laravelData)) {
            parse_str($laravelData, $parsedData);

            $laravelData = is_array($parsedData) ? $parsedData : [];
        }

        if ($laravelData instanceof JsonSerializable) {
            $laravelData = $laravelData->jsonSerialize();
        }

        return is_array($laravelData) ? $laravelData : [];
    }

    /**
     * Normalize the given request options.
     *
     * @param  array  $options
     * @return array
     */
    protected function normalizeRequestOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $options[$key] = match (true) {
                is_array($value) => $this->normalizeRequestOptions($value),
                $value instanceof Stringable => $value->toString(),
                default => $value,
            };
        }

        return $options;
    }

    /**
     * Populate the given response with additional data.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     * @return \Illuminate\Http\Client\Response
     */
    protected function populateResponse(Response $response)
    {
        $response->cookies = $this->cookies;

        $response->transferStats = $this->transferStats;

        return $response;
    }

    /**
     * Build the Guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    public function buildClient()
    {
        return $this->client ?? $this->createClient($this->buildHandlerStack());
    }

    /**
     * Determine if a reusable client is required.
     *
     * @return bool
     */
    protected function requestsReusableClient()
    {
        return ! is_null($this->client) || $this->async;
    }

    /**
     * Retrieve a reusable Guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getReusableClient()
    {
        return $this->client ??= $this->createClient($this->buildHandlerStack());
    }

    /**
     * Create new Guzzle client.
     *
     * @param  \GuzzleHttp\HandlerStack  $handlerStack
     * @return \GuzzleHttp\Client
     */
    public function createClient($handlerStack)
    {
        return new Client([
            'handler' => $handlerStack,
            'cookies' => true,
        ]);
    }

    /**
     * Build the Guzzle client handler stack.
     *
     * @return \GuzzleHttp\HandlerStack
     */
    public function buildHandlerStack()
    {
        return $this->pushHandlers(HandlerStack::create($this->handler));
    }

    /**
     * Add the necessary handlers to the given handler stack.
     *
     * @param  \GuzzleHttp\HandlerStack  $handlerStack
     * @return \GuzzleHttp\HandlerStack
     */
    public function pushHandlers($handlerStack)
    {
        return tap($handlerStack, function ($stack) {
            $this->middleware->each(function ($middleware) use ($stack) {
                $stack->push($middleware);
            });

            $stack->push($this->buildBeforeSendingHandler());
            $stack->push($this->buildRecorderHandler());
            $stack->push($this->buildStubHandler());
        });
    }

    /**
     * Build the before sending handler.
     *
     * @return \Closure
     */
    public function buildBeforeSendingHandler()
    {
        return function ($handler) {
            return function ($request, $options) use ($handler) {
                return $handler($this->runBeforeSendingCallbacks($request, $options), $options);
            };
        };
    }

    /**
     * Build the recorder handler.
     *
     * @return \Closure
     */
    public function buildRecorderHandler()
    {
        return function ($handler) {
            return function ($request, $options) use ($handler) {
                $promise = $handler($request, $options);

                return $promise->then(function ($response) use ($request, $options) {
                    $this->factory?->recordRequestResponsePair(
                        (new Request($request))
                            ->withData($options['laravel_data'])
                            ->setRequestAttributes($this->attributes),
                        $this->newResponse($response)
                    );

                    return $response;
                });
            };
        };
    }

    /**
     * Build the stub handler.
     *
     * @return \Closure
     *
     * @throws \Illuminate\Http\Client\Exceptions\StrayRequestException
     */
    public function buildStubHandler()
    {
        return function ($handler) {
            return function ($request, $options) use ($handler) {
                $response = ($this->stubCallbacks ?? new Collection)
                    ->map
                    ->__invoke(
                        (new Request($request))
                            ->withData($options['laravel_data'])
                            ->setRequestAttributes($this->attributes),
                        $options
                    )
                    ->filter()
                    ->first();

                if (is_null($response)) {
                    if (! $this->isAllowedRequestUrl((string) $request->getUri())) {
                        throw new StrayRequestException((string) $request->getUri());
                    }

                    return $handler($request, $options);
                }

                $response = is_array($response) ? Factory::response($response) : $response;

                $sink = $options['sink'] ?? null;

                if ($sink) {
                    $response->then($this->sinkStubHandler($sink));
                }

                return $response;
            };
        };
    }

    /**
     * Get the sink stub handler callback.
     *
     * @param  string  $sink
     * @return \Closure
     */
    protected function sinkStubHandler($sink)
    {
        return function ($response) use ($sink) {
            $body = $response->getBody()->getContents();

            if (is_string($sink)) {
                file_put_contents($sink, $body);

                return;
            }

            fwrite($sink, $body);
            rewind($sink);
        };
    }

    /**
     * Execute the "before sending" callbacks.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request
     * @param  array  $options
     * @return \Psr\Http\Message\RequestInterface
     */
    public function runBeforeSendingCallbacks($request, array $options)
    {
        return tap($request, function (&$request) use ($options) {
            $this->beforeSendingCallbacks->each(function ($callback) use (&$request, $options) {
                $callbackResult = call_user_func(
                    $callback,
                    (new Request($request))
                        ->withData($options['laravel_data'])
                        ->setRequestAttributes($this->attributes),
                    $options,
                    $this
                );

                if ($callbackResult instanceof RequestInterface) {
                    $request = $callbackResult;
                } elseif ($callbackResult instanceof Request) {
                    $request = $callbackResult->toPsrRequest();
                }
            });
        });
    }

    /**
     * Replace the given options with the current request options.
     *
     * @param  array  ...$options
     * @return array
     */
    public function mergeOptions(...$options)
    {
        return array_replace_recursive(
            array_merge_recursive($this->options, Arr::only($options, $this->mergeableOptions)),
            ...$options
        );
    }

    /**
     * Create a new response instance using the given PSR response.
     *
     * @param  \Psr\Http\Message\MessageInterface  $response
     * @return Response
     */
    protected function newResponse($response)
    {
        return tap(new Response($response), function (Response $laravelResponse) {
            if ($this->truncateExceptionsAt === null) {
                return;
            }

            $this->truncateExceptionsAt === false
                ? $laravelResponse->dontTruncateExceptions()
                : $laravelResponse->truncateExceptionsAt($this->truncateExceptionsAt);
        });
    }

    /**
     * Execute the "after response" callbacks.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     * @return \Illuminate\Http\Client\Response
     */
    protected function runAfterResponseCallbacks(Response $response)
    {
        foreach ($this->afterResponseCallbacks as $callback) {
            $returnedResponse = $callback($response);

            if ($returnedResponse instanceof Response) {
                $response = $returnedResponse;
            }
        }

        return $response;
    }

    /**
     * Register a stub callable that will intercept requests and be able to return stub responses.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function stub($callback)
    {
        $this->stubCallbacks = new Collection($callback);

        return $this;
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
     * Allow stray, unfaked requests entirely, or optionally allow only specific URLs.
     *
     * @param  array<int, string>  $only
     * @return $this
     */
    public function allowStrayRequests(array $only)
    {
        $this->allowedStrayRequestUrls = array_values($only);

        return $this;
    }

    /**
     * Determine if the given URL is allowed as a stray request.
     *
     * @param  string  $url
     * @return bool
     */
    public function isAllowedRequestUrl($url)
    {
        if (! $this->preventStrayRequests) {
            return true;
        }

        foreach ($this->allowedStrayRequestUrls as $pattern) {
            if (Str::is($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Toggle asynchronicity in requests.
     *
     * @template T of bool = true
     *
     * @param  T  $async
     * @return self<T>
     *
     * @phpstan-self-out self<T>
     */
    public function async(bool $async = true)
    {
        $this->async = $async;

        return $this;
    }

    /**
     * Retrieve the pending request promise.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface|null
     */
    public function getPromise()
    {
        return $this->promise;
    }

    /**
     * Dispatch the RequestSending event if a dispatcher is available.
     *
     * @return void
     */
    protected function dispatchRequestSendingEvent()
    {
        if ($dispatcher = $this->factory?->getDispatcher()) {
            $dispatcher->dispatch(new RequestSending($this->request));
        }
    }

    /**
     * Dispatch the ResponseReceived event if a dispatcher is available.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     * @return void
     */
    protected function dispatchResponseReceivedEvent(Response $response)
    {
        if (! ($dispatcher = $this->factory?->getDispatcher()) || ! $this->request) {
            return;
        }

        $dispatcher->dispatch(new ResponseReceived($this->request, $response));
    }

    /**
     * Dispatch the ConnectionFailed event if a dispatcher is available.
     *
     * @param  \Illuminate\Http\Client\Request  $request
     * @param  \Illuminate\Http\Client\ConnectionException  $exception
     * @return void
     */
    protected function dispatchConnectionFailedEvent(Request $request, ConnectionException $exception)
    {
        if ($dispatcher = $this->factory?->getDispatcher()) {
            $dispatcher->dispatch(new ConnectionFailed($request, $exception));
        }
    }

    /**
     * Indicate that request exceptions should be truncated to the given length.
     *
     * @param  int<1, max>  $length
     * @return $this
     */
    public function truncateExceptionsAt(int $length)
    {
        $this->truncateExceptionsAt = $length;

        return $this;
    }

    /**
     * Indicate that request exceptions should not be truncated.
     *
     * @return $this
     */
    public function dontTruncateExceptions()
    {
        $this->truncateExceptionsAt = false;

        return $this;
    }

    /**
     * Handle the given connection exception.
     *
     * @param  \GuzzleHttp\Exception\ConnectException  $e
     * @return void
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function marshalConnectionException(ConnectException $e)
    {
        $exception = new ConnectionException($e->getMessage(), 0, $e);

        $request = (new Request($e->getRequest()))->setRequestAttributes($this->attributes);

        $this->factory?->recordRequestResponsePair(
            $request, null
        );

        $this->dispatchConnectionFailedEvent($request, $exception);

        throw $exception;
    }

    /**
     * Handle the given request exception.
     *
     * @param  \GuzzleHttp\Exception\RequestException  $e
     * @return void
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function marshalRequestExceptionWithoutResponse(RequestException $e)
    {
        $exception = new ConnectionException($e->getMessage(), 0, $e);

        $request = (new Request($e->getRequest()))->setRequestAttributes($this->attributes);

        $this->factory?->recordRequestResponsePair(
            $request, null
        );

        $this->dispatchConnectionFailedEvent($request, $exception);

        throw $exception;
    }

    /**
     * Handle the given request exception.
     *
     * @param  \GuzzleHttp\Exception\RequestException  $e
     * @return void
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function marshalRequestExceptionWithResponse(RequestException $e)
    {
        $response = $this->populateResponse($this->newResponse($e->getResponse()));

        $this->factory?->recordRequestResponsePair(
            (new Request($e->getRequest()))->setRequestAttributes($this->attributes),
            $response
        );

        throw $response->toException() ?? new ConnectionException($e->getMessage(), 0, $e);
    }

    /**
     * Set the client instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Create a new client instance using the given handler.
     *
     * @param  callable  $handler
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Get the pending request options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
