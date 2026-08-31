<?php

namespace Illuminate\Foundation\Testing\Concerns;

use BackedEnum;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Uri;
use Illuminate\Testing\LoggedExceptionCollection;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait MakesHttpRequests
{
    /**
     * Additional headers for the request.
     *
     * @var array
     */
    protected $defaultHeaders = [];

    /**
     * Additional cookies for the request.
     *
     * @var array
     */
    protected $defaultCookies = [];

    /**
     * Additional cookies will not be encrypted for the request.
     *
     * @var array
     */
    protected $unencryptedCookies = [];

    /**
     * Additional server variables for the request.
     *
     * @var array
     */
    protected $serverVariables = [];

    /**
     * Indicates whether redirects should be followed.
     *
     * @var bool
     */
    protected $followRedirects = false;

    /**
     * Indicates whether cookies should be encrypted.
     *
     * @var bool
     */
    protected $encryptCookies = true;

    /**
     * Indicated whether JSON requests should be performed "with credentials" (cookies).
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/withCredentials
     *
     * @var bool
     */
    protected $withCredentials = false;

    /**
     * Define additional headers to be sent with the request.
     *
     * @param  array  $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);

        return $this;
    }

    /**
     * Add a header to be sent with the request.
     *
     * @param  string  $name
     * @param  string  $value
     * @return $this
     */
    public function withHeader(string $name, string $value)
    {
        $this->defaultHeaders[$name] = $value;

        return $this;
    }

    /**
     * Remove a header from the request.
     *
     * @param  string  $name
     * @return $this
     */
    public function withoutHeader(string $name)
    {
        unset($this->defaultHeaders[$name]);

        return $this;
    }

    /**
     * Remove headers from the request.
     *
     * @param  array  $headers
     * @return $this
     */
    public function withoutHeaders(array $headers)
    {
        foreach ($headers as $name) {
            $this->withoutHeader($name);
        }

        return $this;
    }

    /**
     * Add an authorization token for the request.
     *
     * @param  string  $token
     * @param  string  $type
     * @return $this
     */
    public function withToken(string $token, string $type = 'Bearer')
    {
        return $this->withHeader('Authorization', $type.' '.$token);
    }

    /**
     * Add a basic authentication header to the request with the given credentials.
     *
     * @param  string  $username
     * @param  string  $password
     * @return $this
     */
    public function withBasicAuth(string $username, string $password)
    {
        return $this->withToken(base64_encode("$username:$password"), 'Basic');
    }

    /**
     * Remove the authorization token from the request.
     *
     * @return $this
     */
    public function withoutToken()
    {
        return $this->withoutHeader('Authorization');
    }

    /**
     * Flush all the configured headers.
     *
     * @return $this
     */
    public function flushHeaders()
    {
        $this->defaultHeaders = [];

        return $this;
    }

    /**
     * Define a set of server variables to be sent with the requests.
     *
     * @param  array  $server
     * @return $this
     */
    public function withServerVariables(array $server)
    {
        $this->serverVariables = $server;

        return $this;
    }

    /**
     * Disable middleware for the test.
     *
     * @param  string|array|null  $middleware
     * @return $this
     */
    public function withoutMiddleware($middleware = null)
    {
        if (is_null($middleware)) {
            $this->app->instance('middleware.disable', true);

            return $this;
        }

        foreach ((array) $middleware as $abstract) {
            $this->app->instance($abstract, new class
            {
                public function handle($request, $next)
                {
                    return $next($request);
                }
            });
        }

        return $this;
    }

    /**
     * Enable the given middleware for the test.
     *
     * @param  string|array|null  $middleware
     * @return $this
     */
    public function withMiddleware($middleware = null)
    {
        if (is_null($middleware)) {
            unset($this->app['middleware.disable']);

            return $this;
        }

        foreach ((array) $middleware as $abstract) {
            unset($this->app[$abstract]);
        }

        return $this;
    }

    /**
     * Define additional cookies to be sent with the request.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function withCookies(array $cookies)
    {
        $this->defaultCookies = array_merge($this->defaultCookies, $cookies);

        return $this;
    }

    /**
     * Add a cookie to be sent with the request.
     *
     * @param  string  $name
     * @param  string  $value
     * @return $this
     */
    public function withCookie(string $name, string $value)
    {
        $this->defaultCookies[$name] = $value;

        return $this;
    }

    /**
     * Define additional cookies will not be encrypted before sending with the request.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function withUnencryptedCookies(array $cookies)
    {
        $this->unencryptedCookies = array_merge($this->unencryptedCookies, $cookies);

        return $this;
    }

    /**
     * Add a cookie will not be encrypted before sending with the request.
     *
     * @param  string  $name
     * @param  string  $value
     * @return $this
     */
    public function withUnencryptedCookie(string $name, string $value)
    {
        $this->unencryptedCookies[$name] = $value;

        return $this;
    }

    /**
     * Automatically follow any redirects returned from the response.
     *
     * @return $this
     */
    public function followingRedirects()
    {
        $this->followRedirects = true;

        return $this;
    }

    /**
     * Include cookies and authorization headers for JSON requests.
     *
     * @return $this
     */
    public function withCredentials()
    {
        $this->withCredentials = true;

        return $this;
    }

    /**
     * Disable automatic encryption of cookie values.
     *
     * @return $this
     */
    public function disableCookieEncryption()
    {
        $this->encryptCookies = false;

        return $this;
    }

    /**
     * Set the referer header and previous URL session value from a given URL in order to simulate a previous request.
     *
     * @param  string  $url
     * @return $this
     */
    public function from(string $url)
    {
        $this->app['session']->setPreviousUrl($url);

        return $this->withHeader('referer', $url);
    }

    /**
     * Set the referer header and previous URL session value from a given route in order to simulate a previous request.
     *
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @return $this
     */
    public function fromRoute(BackedEnum|string $name, $parameters = [])
    {
        return $this->from($this->app['url']->route($name, $parameters));
    }

    /**
     * Set the Precognition header to "true".
     *
     * @return $this
     */
    public function withPrecognition()
    {
        return $this->withHeader('Precognition', 'true');
    }

    /**
     * Visit the given URI with a GET request.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function get($uri, array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);
        $cookies = $this->prepareCookiesForRequest();

        return $this->call('GET', $uri, [], $cookies, [], $server);
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function getJson($uri, array $headers = [], $options = 0)
    {
        return $this->json('GET', $uri, [], $headers, $options);
    }

    /**
     * Visit the given URI with a POST request.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);
        $cookies = $this->prepareCookiesForRequest();

        return $this->call('POST', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a POST request, expecting a JSON response.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        return $this->json('POST', $uri, $data, $headers, $options);
    }

    /**
     * Visit the given URI with a PUT request.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function put($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);
        $cookies = $this->prepareCookiesForRequest();

        return $this->call('PUT', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a PUT request, expecting a JSON response.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function putJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        return $this->json('PUT', $uri, $data, $headers, $options);
    }

    /**
     * Visit the given URI with a PATCH request.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function patch($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);
        $cookies = $this->prepareCookiesForRequest();

        return $this->call('PATCH', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a PATCH request, expecting a JSON response.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function patchJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        return $this->json('PATCH', $uri, $data, $headers, $options);
    }

    /**
     * Visit the given URI with a DELETE request.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);
        $cookies = $this->prepareCookiesForRequest();

        return $this->call('DELETE', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a DELETE request, expecting a JSON response.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function deleteJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        return $this->json('DELETE', $uri, $data, $headers, $options);
    }

    /**
     * Visit the given URI with an OPTIONS request.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function options($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        $cookies = $this->prepareCookiesForRequest();

        return $this->call('OPTIONS', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with an OPTIONS request, expecting a JSON response.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function optionsJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        return $this->json('OPTIONS', $uri, $data, $headers, $options);
    }

    /**
     * Visit the given URI with a HEAD request.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function head($uri, array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        $cookies = $this->prepareCookiesForRequest();

        return $this->call('HEAD', $uri, [], $cookies, [], $server);
    }

    /**
     * Call the given URI with a JSON request.
     *
     * @param  string  $method
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        $files = $this->extractFilesFromDataArray($data);

        $content = json_encode($data, $options);

        $headers = array_merge([
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);

        return $this->call(
            $method,
            $uri,
            [],
            $this->prepareCookiesForJsonRequest(),
            $files,
            $this->transformHeadersToServerVars($headers),
            $content
        );
    }

    /**
     * Call the given URI and return the Response.
     *
     * @param  string  $method
     * @param  \Illuminate\Support\Uri|string  $uri
     * @param  array  $parameters
     * @param  array  $cookies
     * @param  array  $files
     * @param  array  $server
     * @param  string|null  $content
     * @return \Illuminate\Testing\TestResponse
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $kernel = $this->app->make(HttpKernel::class);

        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest($uri), $method, $parameters,
            $cookies, $files, array_replace($this->serverVariables, $server), $content
        );

        $response = $kernel->handle(
            $request = $this->createTestRequest($symfonyRequest)
        );

        $kernel->terminate($request, $response);

        if ($this->followRedirects) {
            $response = $this->followRedirects($response);
        }

        return $this->createTestResponse($response, $request);
    }

    /**
     * Turn the given URI into a fully-qualified URL.
     *
     * @param  \Illuminate\Support\Uri|string  $uri
     * @return string
     */
    protected function prepareUrlForRequest($uri)
    {
        $uri = $uri instanceof Uri ? $uri->value() : $uri;

        if (str_starts_with($uri, '/')) {
            $uri = substr($uri, 1);
        }

        return trim(url($uri), '/');
    }

    /**
     * Transform headers array to array of $_SERVER vars with HTTP_* format.
     *
     * @param  array  $headers
     * @return array
     */
    protected function transformHeadersToServerVars(array $headers)
    {
        return (new Collection(array_merge($this->defaultHeaders, $headers)))->mapWithKeys(function ($value, $name) {
            $name = strtr(strtoupper($name), '-', '_');

            return [$this->formatServerHeaderKey($name) => $value];
        })->all();
    }

    /**
     * Format the header name for the server array.
     *
     * @param  string  $name
     * @return string
     */
    protected function formatServerHeaderKey($name)
    {
        if (! str_starts_with($name, 'HTTP_') && $name !== 'CONTENT_TYPE' && $name !== 'REMOTE_ADDR') {
            return 'HTTP_'.$name;
        }

        return $name;
    }

    /**
     * Extract the file uploads from the given data array.
     *
     * @param  array  $data
     * @return array
     */
    protected function extractFilesFromDataArray(&$data)
    {
        $files = [];

        foreach ($data as $key => $value) {
            if ($value instanceof SymfonyUploadedFile) {
                $files[$key] = $value;

                unset($data[$key]);
            }

            if (is_array($value)) {
                $files[$key] = $this->extractFilesFromDataArray($value);

                $data[$key] = $value;
            }
        }

        return $files;
    }

    /**
     * If enabled, encrypt cookie values for request.
     *
     * @return array
     */
    protected function prepareCookiesForRequest()
    {
        if (! $this->encryptCookies) {
            return array_merge($this->defaultCookies, $this->unencryptedCookies);
        }

        return (new Collection($this->defaultCookies))
            ->map(fn ($value, $key) => encrypt(CookieValuePrefix::create($key, app('encrypter')->getKey()).$value, false))
            ->merge($this->unencryptedCookies)
            ->all();
    }

    /**
     * If enabled, add cookies for JSON requests.
     *
     * @return array
     */
    protected function prepareCookiesForJsonRequest()
    {
        return $this->withCredentials ? $this->prepareCookiesForRequest() : [];
    }

    /**
     * Follow a redirect chain until a non-redirect is received.
     *
     * @param  \Illuminate\Http\Response|\Illuminate\Testing\TestResponse  $response
     * @return \Illuminate\Http\Response|\Illuminate\Testing\TestResponse
     */
    protected function followRedirects($response)
    {
        $this->followRedirects = false;

        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }

    /**
     * Create the request instance used for testing from the given Symfony request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $symfonyRequest
     * @return \Illuminate\Http\Request
     */
    protected function createTestRequest($symfonyRequest)
    {
        return Request::createFromBase($symfonyRequest);
    }

    /**
     * Create the test response instance from the given response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Testing\TestResponse
     */
    protected function createTestResponse($response, $request)
    {
        return tap(TestResponse::fromBaseResponse($response, $request), function ($response) {
            $response->withExceptions(
                $this->app->bound(LoggedExceptionCollection::class)
                    ? $this->app->make(LoggedExceptionCollection::class)
                    : new LoggedExceptionCollection
            );
        });
    }
}
