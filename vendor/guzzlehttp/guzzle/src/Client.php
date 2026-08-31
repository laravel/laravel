<?php

namespace GuzzleHttp;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Handler\CurlShareHandleState;
use GuzzleHttp\Handler\CurlVersion;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @final
 */
class Client implements ClientInterface, \Psr\Http\Client\ClientInterface
{
    use ClientTrait;

    /**
     * @var array Default request options
     */
    private $config;

    /**
     * Clients accept an array of constructor parameters.
     *
     * Here's an example of creating a client using a base_uri and an array of
     * default request options to apply to each request:
     *
     *     $client = new Client([
     *         'base_uri'        => 'http://www.foo.com/1.0/',
     *         'timeout'         => 0,
     *         'allow_redirects' => false,
     *         'proxy'           => '192.168.16.1:10'
     *     ]);
     *
     * Client configuration settings include the following options:
     *
     * - handler: (callable) Function that transfers HTTP requests over the
     *   wire. The function is called with a Psr7\Http\Message\RequestInterface
     *   and array of transfer options, and must return a
     *   GuzzleHttp\Promise\PromiseInterface that is fulfilled with a
     *   Psr7\Http\Message\ResponseInterface on success.
     *   If no handler is provided, a default handler will be created
     *   that enables all of the request options below by attaching all of the
     *   default middleware to the handler.
     * - base_uri: (string|UriInterface) Base URI of the client that is merged
     *   into relative URIs. Can be a string or instance of UriInterface.
     * - transport_sharing: (string|null) Transport sharing mode for the
     *   default handler. Accepts TransportSharing::* or null. Defaults to null.
     * - max_host_connections: (int|null) Maximum concurrent connections per
     *   host, applied by the default CurlMultiHandler. The default stream
     *   fallback receives the cap as a marker only: it rejects enabled
     *   response streaming ("stream" => true) and does not limit overlapping
     *   buffered calls.
     * - max_total_connections: (int|null) Maximum concurrent connections
     *   overall, applied by the default CurlMultiHandler. The default stream
     *   fallback receives the cap as a marker only: it rejects enabled
     *   response streaming ("stream" => true) and does not limit overlapping
     *   buffered calls.
     * - multiplex: (string|null) Multiplexing::NONE to disable multiplexing on
     *   the default CurlMultiHandler; the value also becomes the default
     *   "multiplex" request option. Other Multiplexing::* values act as the
     *   default request option only.
     * - **: any request option
     *
     * @param array $config Client configuration settings.
     *
     * @see RequestOptions for a list of available request options.
     */
    public function __construct(array $config = [])
    {
        $handlerOptions = [];
        foreach (['max_host_connections', 'max_total_connections'] as $capOption) {
            if (\array_key_exists($capOption, $config)) {
                if ($config[$capOption] !== null) {
                    $handlerOptions[$capOption] = $config[$capOption];
                }

                unset($config[$capOption]);
            }
        }

        // Deliberately not unset: the value also becomes the default
        // "multiplex" request option, which the configured handler accepts.
        $handlerMultiplex = ($config['multiplex'] ?? null) === Multiplexing::NONE;

        $transportSharing = \array_key_exists('transport_sharing', $config) ? $config['transport_sharing'] : null;
        $transportSharingMode = CurlShareHandleState::normalizeMode($transportSharing, 'transport_sharing');
        unset($config['transport_sharing']);

        if (!isset($config['handler'])) {
            if ($transportSharingMode !== TransportSharing::NONE) {
                $handlerOptions['transport_sharing'] = $transportSharingMode;
            }

            if ($handlerMultiplex) {
                $handlerOptions['multiplex'] = Multiplexing::NONE;
            }

            $config['handler'] = $handlerOptions === []
                ? HandlerStack::create()
                : HandlerStack::create(Utils::chooseHandler($handlerOptions));
        } elseif (!\is_callable($config['handler'])) {
            throw new InvalidArgumentException('handler must be a callable');
        } elseif ($handlerOptions !== []) {
            throw new InvalidArgumentException('The "max_host_connections" and "max_total_connections" client options require Guzzle to create the default handler. Configure the options on the CurlMultiHandler constructor to apply numeric connection caps, or on the StreamHandler constructor to reject enabled response streaming, when providing a custom handler.');
        } elseif ($transportSharingMode === TransportSharing::HANDLER_REQUIRE) {
            throw new InvalidArgumentException('The "transport_sharing" client option can only require sharing when Guzzle creates the default handler. Configure the "transport_sharing" option on CurlHandler or CurlMultiHandler when providing a custom cURL handler.');
        }

        // Convert the base_uri to a UriInterface
        if (isset($config['base_uri'])) {
            $config['base_uri'] = Psr7\Utils::uriFor($config['base_uri']);
        }

        $this->configureDefaults($config);
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return PromiseInterface|ResponseInterface
     *
     * @deprecated Client::__call will be removed in guzzlehttp/guzzle:8.0.
     */
    public function __call($method, $args)
    {
        \trigger_deprecation('guzzlehttp/guzzle', '7.1', '%s::%s() is deprecated and will be removed in 8.0.', __CLASS__, __FUNCTION__);

        if (\count($args) < 1) {
            throw new InvalidArgumentException('Magic request methods require a URI and optional options array');
        }

        $uri = $args[0];
        $opts = $args[1] ?? [];

        $isAsync = \substr($method, -5) === 'Async';
        $method = $isAsync ? \substr($method, 0, -5) : $method;
        $method = Psr7\Utils::asciiToUpper($method);

        return $isAsync
            ? $this->requestAsync($method, $uri, $opts)
            : $this->request($method, $uri, $opts);
    }

    /**
     * Asynchronously send an HTTP request.
     *
     * @param array $options Request options to apply to the given
     *                       request and to the transfer. See {@see RequestOptions}.
     */
    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        // Merge the base URI into the request URI if needed.
        $options = $this->prepareDefaults($options);

        return $this->transfer(
            $request->withUri($this->buildUri($request->getUri(), $options), $request->hasHeader('Host')),
            $options
        );
    }

    /**
     * Send an HTTP request.
     *
     * @param array $options Request options to apply to the given
     *                       request and to the transfer. See {@see RequestOptions}.
     *
     * @throws GuzzleException
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        $options[RequestOptions::SYNCHRONOUS] = true;

        return $this->sendAsync($request, $options)->wait();
    }

    /**
     * The HttpClient PSR (PSR-18) specify this method.
     *
     * {@inheritDoc}
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $options[RequestOptions::SYNCHRONOUS] = true;
        $options[RequestOptions::ALLOW_REDIRECTS] = false;
        $options[RequestOptions::HTTP_ERRORS] = false;

        return $this->sendAsync($request, $options)->wait();
    }

    /**
     * Create and send an asynchronous HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well. Use an array to provide a URL
     * template and additional variables to use in the URL template expansion.
     *
     * @param string              $method  HTTP method
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply. See {@see RequestOptions}.
     */
    public function requestAsync(string $method, $uri = '', array $options = []): PromiseInterface
    {
        $normalizedMethod = Psr7\Utils::asciiToUpper($method);
        if ($method !== $normalizedMethod) {
            \trigger_deprecation(
                'guzzlehttp/guzzle',
                '7.11',
                'Passing a non-uppercase HTTP method to Client::requestAsync() is deprecated; guzzlehttp/guzzle 8.0 will preserve HTTP method casing. Pass an uppercase method explicitly if uppercase is required.'
            );
            $method = $normalizedMethod;
        }

        $options = $this->prepareDefaults($options);
        // Remove request modifying parameter because it can be done up-front.
        $headers = $options['headers'] ?? [];
        $droppedHeaderNames = self::castDeprecatedHeaderOptionValues($headers);
        if ($droppedHeaderNames !== [] && isset($options['_conditional'])) {
            $options['_conditional'] = Psr7\Utils::caselessRemove($droppedHeaderNames, $options['_conditional']);
        }
        $body = $options['body'] ?? null;
        $version = self::normalizeProtocolVersion($options['version'] ?? '1.1');
        // Merge the URI into the base URI.
        $uri = $this->buildUri(Psr7\Utils::uriFor($uri), $options);
        if (\is_array($body)) {
            throw $this->invalidBody();
        }
        $body = self::createBodyStream($body);
        $request = new Psr7\Request($method, $uri, $headers, $body, $version);
        // Remove the option so that they are not doubly-applied.
        unset($options['headers'], $options['body'], $options['version']);

        return $this->transfer($request, $options);
    }

    /**
     * Create and send an HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string              $method  HTTP method.
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply. See {@see RequestOptions}.
     *
     * @throws GuzzleException
     */
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        $normalizedMethod = Psr7\Utils::asciiToUpper($method);
        if ($method !== $normalizedMethod) {
            \trigger_deprecation(
                'guzzlehttp/guzzle',
                '7.11',
                'Passing a non-uppercase HTTP method to Client::request() is deprecated; guzzlehttp/guzzle 8.0 will preserve HTTP method casing. Pass an uppercase method explicitly if uppercase is required.'
            );
            $method = $normalizedMethod;
        }

        $options[RequestOptions::SYNCHRONOUS] = true;

        return $this->requestAsync($method, $uri, $options)->wait();
    }

    /**
     * Get a client configuration option.
     *
     * These options include default request options of the client, a "handler"
     * (if utilized by the concrete client), and a "base_uri" if utilized by
     * the concrete client.
     *
     * @param string|null $option The config option to retrieve.
     *
     * @return mixed
     */
    public function getConfig(?string $option = null)
    {
        return $option === null
            ? $this->config
            : ($this->config[$option] ?? null);
    }

    private function buildUri(UriInterface $uri, array $config): UriInterface
    {
        if (isset($config['base_uri'])) {
            $uri = Psr7\UriResolver::resolve(Psr7\Utils::uriFor($config['base_uri']), $uri);
        }

        $idnOptions = Utils::normalizeIdnConversionOption($config['idn_conversion'] ?? null);
        if ($idnOptions !== null) {
            $uri = Utils::idnUriConvert($uri, $idnOptions);
        }

        if ($uri->getScheme() === '' && $uri->getHost() !== '') {
            $uri = $uri->withScheme('http');
        }

        return $uri;
    }

    /**
     * Configures the default options for a client.
     */
    private function configureDefaults(array $config): void
    {
        $defaults = [
            'allow_redirects' => RedirectMiddleware::$defaultSettings,
            'http_errors' => true,
            'decode_content' => true,
            'verify' => true,
            'cookies' => false,
            'idn_conversion' => false,
            'protocols' => ['http', 'https'],
        ];

        // Use the standard Linux HTTP_PROXY and HTTPS_PROXY if set.

        // We can only trust the HTTP_PROXY environment variable in a CLI
        // process due to the fact that PHP has no reliable mechanism to
        // get environment variables that start with "HTTP_".
        if (\PHP_SAPI === 'cli' && ($proxy = Utils::getenv('HTTP_PROXY'))) {
            $defaults['proxy']['http'] = $proxy;
        }

        if ($proxy = Utils::getenv('HTTPS_PROXY')) {
            $defaults['proxy']['https'] = $proxy;
        }

        if ($noProxy = Utils::getenv('NO_PROXY')) {
            $cleanedNoProxy = \str_replace(' ', '', $noProxy);
            $defaults['proxy']['no'] = \explode(',', $cleanedNoProxy);
        }

        $this->config = $config + $defaults;

        if (!empty($config['cookies']) && $config['cookies'] === true) {
            $this->config['cookies'] = new CookieJar();
        }

        // Add the default user-agent header.
        if (!isset($this->config['headers'])) {
            $this->config['headers'] = ['User-Agent' => Utils::defaultUserAgent()];
        } else {
            // Add the User-Agent header if one was not already set.
            $hasUserAgent = false;
            foreach (\array_keys($this->config['headers']) as $name) {
                if (Psr7\Utils::asciiToLower((string) $name) === 'user-agent') {
                    $hasUserAgent = true;
                    break;
                }
            }

            if (!$hasUserAgent) {
                $this->config['headers']['User-Agent'] = Utils::defaultUserAgent();
            }
        }

        if (\is_array($this->config['headers'])) {
            self::warnAboutInvalidHeaderOptionTypes($this->config['headers']);
            self::castDeprecatedHeaderOptionValues($this->config['headers']);
        }
    }

    /**
     * Merges default options into the array.
     *
     * @param array $options Options to modify by reference
     */
    private function prepareDefaults(array $options): array
    {
        self::warnAboutRequestLevelHandler($options);

        $defaults = $this->config;

        if (!empty($defaults['headers'])) {
            // Default headers are only added if they are not present.
            $defaults['_conditional'] = $defaults['headers'];
            unset($defaults['headers']);
        }

        // Special handling for headers is required as they are added as
        // conditional headers and as headers passed to a request ctor.
        if (\array_key_exists('headers', $options)) {
            // Allows default headers to be unset.
            if ($options['headers'] === null) {
                $defaults['_conditional'] = [];
                unset($options['headers']);
            } elseif (!\is_array($options['headers'])) {
                throw new InvalidArgumentException('headers must be an array');
            }
        }

        // Shallow merge defaults underneath options.
        $result = $options + $defaults;

        // Remove null values.
        foreach ($result as $k => $v) {
            if ($v === null) {
                unset($result[$k]);
            }
        }

        self::warnAboutInvalidRequestOptionTypes($result);

        return self::normalizeDeprecatedRequestOptionValues($result);
    }

    /**
     * Normalize values that guzzlehttp/guzzle 8.0 rejects only after the
     * corresponding 7.x deprecation has already been emitted.
     *
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private static function normalizeDeprecatedRequestOptionValues(array $options): array
    {
        self::normalizeDeprecatedAuthOptionValues($options);
        self::normalizeDeprecatedTlsFileOptionValues($options, 'cert');
        self::normalizeDeprecatedTlsFileOptionValues($options, 'ssl_key');
        self::normalizeDeprecatedStringOptionValues($options);
        self::normalizeDeprecatedNumericOptionValues($options);
        self::normalizeDeprecatedIntegerOptionValues($options);

        return $options;
    }

    /**
     * @param mixed $value
     */
    private static function canStringifyDeprecatedValue($value): bool
    {
        return $value === null
            || \is_scalar($value)
            || (\is_object($value) && \method_exists($value, '__toString'));
    }

    /**
     * @param mixed $value
     */
    private static function stringifyDeprecatedValue($value): string
    {
        if (\is_float($value) && !\is_finite($value)) {
            return \is_nan($value) ? 'NAN' : ($value > 0 ? 'INF' : '-INF');
        }

        if ($value === null) {
            return '';
        }

        if (\is_scalar($value)) {
            return (string) $value;
        }

        if (\is_object($value) && \method_exists($value, '__toString')) {
            return $value->__toString();
        }

        throw new \LogicException('Value is not stringable.');
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function normalizeDeprecatedAuthOptionValues(array &$options): void
    {
        if (!isset($options['auth']) || !\is_array($options['auth']) || $options['auth'] === []) {
            return;
        }

        foreach ([0, 1] as $index) {
            if (
                \array_key_exists($index, $options['auth'])
                && !\is_string($options['auth'][$index])
                && self::canStringifyDeprecatedValue($options['auth'][$index])
            ) {
                $options['auth'][$index] = self::stringifyDeprecatedValue($options['auth'][$index]);
            }
        }

        if (
            \array_key_exists(2, $options['auth'])
            && $options['auth'][2] !== null
            && !\is_string($options['auth'][2])
            && self::canStringifyDeprecatedValue($options['auth'][2])
        ) {
            $options['auth'][2] = self::stringifyDeprecatedValue($options['auth'][2]);
        }
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function normalizeDeprecatedTlsFileOptionValues(array &$options, string $option): void
    {
        if (!isset($options[$option]) || !\is_array($options[$option])) {
            return;
        }

        foreach ([0, 1] as $index) {
            if (
                \array_key_exists($index, $options[$option])
                && $options[$option][$index] !== null
                && !\is_string($options[$option][$index])
                && self::canStringifyDeprecatedValue($options[$option][$index])
            ) {
                $options[$option][$index] = self::stringifyDeprecatedValue($options[$option][$index]);
            }
        }
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function normalizeDeprecatedStringOptionValues(array &$options): void
    {
        foreach (['cert_type', 'force_ip_resolve', 'ssl_key_type'] as $option) {
            if (
                \array_key_exists($option, $options)
                && !\is_string($options[$option])
                && self::canStringifyDeprecatedValue($options[$option])
            ) {
                $options[$option] = self::stringifyDeprecatedValue($options[$option]);
            }
        }
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function normalizeDeprecatedNumericOptionValues(array &$options): void
    {
        foreach (['connect_timeout', 'delay', 'read_timeout', 'timeout'] as $option) {
            if (
                \array_key_exists($option, $options)
                && \is_string($options[$option])
                && \is_numeric($options[$option])
            ) {
                $options[$option] = $options[$option] + 0;
            }
        }
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function normalizeDeprecatedIntegerOptionValues(array &$options): void
    {
        foreach (['crypto_method', 'crypto_method_max', 'retries'] as $option) {
            if (!\array_key_exists($option, $options)) {
                continue;
            }

            if (\is_string($options[$option]) && \preg_match('/^-?\d+$/D', $options[$option]) === 1) {
                $options[$option] = (int) $options[$option];
            } elseif (
                \is_float($options[$option])
                && \is_finite($options[$option])
                && $options[$option] === (float) (int) $options[$option]
            ) {
                $options[$option] = (int) $options[$option];
            }
        }
    }

    private static function warnAboutRequestLevelHandler(array $options): void
    {
        if (!\array_key_exists('handler', $options)) {
            return;
        }

        \trigger_deprecation(
            'guzzlehttp/guzzle',
            '7.12',
            'Passing the "handler" request option is deprecated; guzzlehttp/guzzle 8.0 will ignore request-level handlers. Configure the handler when creating the Client, or use a separate Client instance for requests that need a different handler.'
        );
    }

    private static function warnAboutInvalidRequestOptionTypes(array $options): void
    {
        if (isset($options['handler']) && !\is_callable($options['handler'])) {
            self::warnInvalidRequestOptionType('handler', 'callable', $options['handler']);
        }

        if (isset($options['allow_redirects']) && \is_array($options['allow_redirects'])) {
            self::warnAboutInvalidAllowRedirectsOptionTypes($options['allow_redirects']);
        } elseif (isset($options['allow_redirects']) && !\is_bool($options['allow_redirects'])) {
            self::warnInvalidRequestOptionType('allow_redirects', 'bool|array', $options['allow_redirects'], '7.13');
        }

        if (isset($options['auth'])) {
            self::warnAboutInvalidAuthOptionTypes($options['auth']);
        }

        if (isset($options['body']) && \is_array($options['body'])) {
            self::warnInvalidRequestOptionType('body', 'resource|string|null|int|float|bool|StreamInterface|(callable&object)|\Iterator|\Stringable', $options['body']);
        }

        self::warnAboutInvalidTlsFileOptionTypes($options, 'cert');
        self::warnIfPresentAndNotString($options, 'cert_type');
        self::warnIfPresentAndNotNumber($options, 'connect_timeout');
        self::warnIfPresentAndNotInt($options, 'crypto_method');
        self::warnIfPresentAndNotInt($options, 'crypto_method_max', null, '7.13');
        self::warnIfPresentAndNotBoolOrResource($options, 'debug');
        self::warnIfPresentAndNotBoolOrString($options, 'decode_content');
        self::warnIfPresentAndNotNumber($options, 'delay');
        if (isset($options['delay']) && \is_numeric($options['delay'])) {
            $delay = (float) $options['delay'];
            if (!\is_finite($delay) || $delay < 0.0) {
                self::warnInvalidRequestOptionType('delay', 'finite int|float greater than or equal to 0', $options['delay'], '7.13');
            }
        }
        self::warnIfPresentAndNotBoolOrInt($options, 'expect');

        if (isset($options['form_params'])) {
            self::warnAboutInvalidFormParamTypes($options['form_params']);
        }

        if (isset($options['force_ip_resolve']) && !\is_string($options['force_ip_resolve'])) {
            self::warnInvalidRequestOptionType('force_ip_resolve', 'string', $options['force_ip_resolve']);
        }

        if (
            isset($options['force_ip_resolve'])
            && \is_string($options['force_ip_resolve'])
            && $options['force_ip_resolve'] !== 'v4'
            && $options['force_ip_resolve'] !== 'v6'
        ) {
            self::warnInvalidRequestOptionType('force_ip_resolve', '"v4"|"v6"', $options['force_ip_resolve'], '7.13');
        }

        if (isset($options['headers'])) {
            self::warnAboutInvalidHeaderOptionTypes($options['headers']);
        }

        self::warnIfPresentAndNotBool($options, 'http_errors');

        if (isset($options['multipart'])) {
            self::warnAboutInvalidMultipartOptionTypes($options['multipart']);
        }

        self::warnIfPresentAndNotCallable($options, 'on_headers');
        self::warnIfPresentAndNotCallable($options, 'on_stats');
        self::warnIfPresentAndNotCallable($options, 'on_trailers', null, '7.14');
        self::warnIfPresentAndNotCallable($options, 'progress');
        self::warnIfPresentAndNotStringArray($options, 'protocols', true);
        self::warnAboutInvalidProtocolValues($options, 'protocols');
        self::warnAboutInvalidProxyOptionTypes($options);

        self::warnIfPresentAndNotNumber($options, 'read_timeout');
        self::warnIfPresentAndNotInt($options, 'retries');

        if (isset($options['sink']) && !\is_bool($options['sink']) && !\is_resource($options['sink']) && !\is_string($options['sink']) && !$options['sink'] instanceof StreamInterface) {
            self::warnInvalidRequestOptionType('sink', 'resource|string|StreamInterface', $options['sink']);
        }

        self::warnAboutInvalidTlsFileOptionTypes($options, 'ssl_key');
        self::warnIfPresentAndNotString($options, 'ssl_key_type');
        self::warnIfPresentAndNotBool($options, 'stream');
        self::warnIfPresentAndNotArray($options, 'stream_context', 'array<array-key, mixed>');
        self::warnIfPresentAndNotBool($options, 'synchronous');
        self::warnIfPresentAndNotNumber($options, 'timeout');
        self::warnIfPresentAndNotBoolOrString($options, 'verify');
        self::warnIfPresentAndNotStringOrNumber($options, 'version');
        self::warnIfPresentAndNotArray($options, 'curl', 'array<int|string, mixed>');

        if (isset($options['cookies']) && $options['cookies'] === true) {
            self::warnInvalidRequestOptionType('cookies', 'false|CookieJarInterface', $options['cookies']);
        }

        if (
            isset($options['cookies'])
            && $options['cookies'] !== false
            && $options['cookies'] !== true
            && !($options['cookies'] instanceof CookieJarInterface)
        ) {
            self::warnInvalidRequestOptionType('cookies', 'false|CookieJarInterface', $options['cookies'], '7.13');
        }
    }

    private static function warnAboutInvalidAllowRedirectsOptionTypes(array $allowRedirects): void
    {
        self::warnIfPresentAndNotInt($allowRedirects, 'max', 'allow_redirects.max');
        self::warnIfPresentAndNotBool($allowRedirects, 'strict', 'allow_redirects.strict');
        self::warnIfPresentAndNotBool($allowRedirects, 'referer', 'allow_redirects.referer');
        self::warnIfPresentAndNotStringArray($allowRedirects, 'protocols', true, 'allow_redirects.protocols');
        self::warnAboutInvalidProtocolValues($allowRedirects, 'protocols', 'allow_redirects.protocols');
        self::warnIfPresentAndNotCallable($allowRedirects, 'on_redirect', 'allow_redirects.on_redirect');
        self::warnIfPresentAndNotBool($allowRedirects, 'track_redirects', 'allow_redirects.track_redirects');
    }

    /**
     * @param mixed $auth
     */
    private static function warnAboutInvalidAuthOptionTypes($auth): void
    {
        if ($auth === false || \is_string($auth) || $auth === []) {
            return;
        }

        if (!\is_array($auth)) {
            self::warnInvalidRequestOptionType('auth', 'array{0: string, 1: string, 2?: string|null}|string|false|null', $auth);

            return;
        }

        if (!\array_key_exists(0, $auth) || !\is_string($auth[0])) {
            self::warnInvalidRequestOptionType('auth.0', 'string', $auth[0] ?? null);
        }

        if (!\array_key_exists(1, $auth) || !\is_string($auth[1])) {
            self::warnInvalidRequestOptionType('auth.1', 'string', $auth[1] ?? null);
        }

        if (\array_key_exists(2, $auth) && $auth[2] !== null && !\is_string($auth[2])) {
            self::warnInvalidRequestOptionType('auth.2', 'string|null', $auth[2]);
        }
    }

    /**
     * @param mixed $value
     */
    private static function warnAboutInvalidFormParamTypes($value): void
    {
        if (!\is_array($value)) {
            self::warnInvalidRequestOptionType('form_params', 'array<array-key, string|int|float|bool|null|array>', $value);

            return;
        }

        self::warnAboutInvalidFormParamArray($value, 'form_params');
    }

    private static function warnAboutInvalidFormParamArray(array $values, string $path): bool
    {
        foreach ($values as $key => $item) {
            $itemPath = $path.'.'.(string) $key;
            if (\is_array($item)) {
                if (!self::warnAboutInvalidFormParamArray($item, $itemPath)) {
                    return false;
                }

                continue;
            }

            if ($item !== null && !\is_scalar($item)) {
                self::warnInvalidRequestOptionType($itemPath, 'string|int|float|bool|null|array', $item);

                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $headers
     */
    private static function warnAboutInvalidHeaderOptionTypes($headers): void
    {
        if (!\is_array($headers)) {
            self::warnInvalidRequestOptionType('headers', 'array<array-key, string|non-empty-array<array-key, string>>|null', $headers);

            return;
        }

        foreach ($headers as $name => $value) {
            $path = 'headers.'.(string) $name;
            if (\is_array($value)) {
                if ($value === []) {
                    self::warnInvalidRequestOptionType($path, 'string|non-empty-array<array-key, string>', $value);

                    break;
                }

                foreach ($value as $index => $item) {
                    if (!\is_string($item)) {
                        self::warnInvalidRequestOptionType($path.'.'.(string) $index, 'string', $item);

                        break 2;
                    }
                }
            } elseif (!\is_string($value)) {
                self::warnInvalidRequestOptionType($path, 'string|non-empty-array<array-key, string>', $value);

                break;
            }
        }
    }

    /**
     * @param mixed $multipart
     */
    private static function warnAboutInvalidMultipartOptionTypes($multipart): void
    {
        if (!\is_array($multipart)) {
            self::warnInvalidRequestOptionType('multipart', 'array<array-key, array{name: string|int, contents: mixed, headers?: array<array-key, string>, filename?: string}>', $multipart);

            return;
        }

        foreach ($multipart as $index => $part) {
            $path = 'multipart.'.(string) $index;
            if (!\is_array($part)) {
                self::warnInvalidRequestOptionType($path, 'array{name: string|int, contents: mixed, headers?: array<array-key, string>, filename?: string}', $part);

                return;
            }

            if (!\array_key_exists('name', $part) || (!\is_string($part['name']) && !\is_int($part['name']))) {
                self::warnInvalidRequestOptionType($path.'.name', 'string|int', $part['name'] ?? null);
            }

            if (!\array_key_exists('contents', $part)) {
                self::warnInvalidRequestOptionType($path, 'array{name: string|int, contents: mixed, headers?: array<array-key, string>, filename?: string}', $part);
            }

            if (\array_key_exists('headers', $part)) {
                if (!\is_array($part['headers'])) {
                    self::warnInvalidRequestOptionType($path.'.headers', 'array<array-key, string>', $part['headers']);
                } else {
                    foreach ($part['headers'] as $name => $value) {
                        if (!\is_string($value)) {
                            self::warnInvalidRequestOptionType($path.'.headers.'.(string) $name, 'string', $value);

                            break 2;
                        }
                    }
                }
            }

            if (\array_key_exists('filename', $part) && !\is_string($part['filename'])) {
                self::warnInvalidRequestOptionType($path.'.filename', 'string', $part['filename']);
            }
        }
    }

    private static function warnAboutInvalidProxyOptionTypes(array $options): void
    {
        if (!isset($options['proxy'])) {
            return;
        }

        if (!\is_string($options['proxy']) && !\is_array($options['proxy'])) {
            self::warnInvalidRequestOptionType('proxy', 'string|array{http?: string|null, https?: string|null, no?: string|array<array-key, string>|null}', $options['proxy']);

            return;
        }

        if (!\is_array($options['proxy'])) {
            return;
        }

        foreach (['http', 'https'] as $scheme) {
            if (\array_key_exists($scheme, $options['proxy']) && $options['proxy'][$scheme] !== null && !\is_string($options['proxy'][$scheme])) {
                self::warnInvalidRequestOptionType('proxy.'.$scheme, 'string|null', $options['proxy'][$scheme]);
            }
        }

        if (!\array_key_exists('no', $options['proxy']) || $options['proxy']['no'] === null) {
            return;
        }

        if (\is_string($options['proxy']['no'])) {
            return;
        }

        if (!\is_array($options['proxy']['no'])) {
            self::warnInvalidRequestOptionType('proxy.no', 'string|array<array-key, string>|null', $options['proxy']['no']);

            return;
        }

        foreach ($options['proxy']['no'] as $index => $noProxy) {
            if (!\is_string($noProxy)) {
                self::warnInvalidRequestOptionType('proxy.no.'.(string) $index, 'string', $noProxy);

                return;
            }
        }
    }

    private static function warnAboutInvalidTlsFileOptionTypes(array $options, string $option): void
    {
        if (!isset($options[$option])) {
            return;
        }

        if (\is_string($options[$option])) {
            return;
        }

        if (!\is_array($options[$option])) {
            self::warnInvalidRequestOptionType($option, 'string|array{0: string, 1?: string}', $options[$option]);

            return;
        }

        if (!\array_key_exists(0, $options[$option]) || !\is_string($options[$option][0])) {
            self::warnInvalidRequestOptionType($option.'.0', 'string', $options[$option][0] ?? null);
        }

        if (\array_key_exists(1, $options[$option]) && $options[$option][1] !== null && !\is_string($options[$option][1])) {
            self::warnInvalidRequestOptionType($option.'.1', 'string|null', $options[$option][1]);
        }
    }

    private static function warnIfPresentAndNotArray(array $options, string $option, string $expected): void
    {
        if (\array_key_exists($option, $options) && !\is_array($options[$option])) {
            self::warnInvalidRequestOptionType($option, $expected, $options[$option]);
        }
    }

    private static function warnIfPresentAndNotBool(array $options, string $option, ?string $path = null): void
    {
        if (\array_key_exists($option, $options) && !\is_bool($options[$option])) {
            self::warnInvalidRequestOptionType($path ?? $option, 'bool', $options[$option]);
        }
    }

    private static function warnIfPresentAndNotBoolOrInt(array $options, string $option): void
    {
        if (\array_key_exists($option, $options) && !\is_bool($options[$option]) && !\is_int($options[$option])) {
            self::warnInvalidRequestOptionType($option, 'bool|int', $options[$option]);
        }
    }

    private static function warnIfPresentAndNotBoolOrResource(array $options, string $option): void
    {
        if (\array_key_exists($option, $options) && !\is_bool($options[$option]) && !\is_resource($options[$option])) {
            self::warnInvalidRequestOptionType($option, 'bool|resource', $options[$option]);
        }
    }

    private static function warnIfPresentAndNotBoolOrString(array $options, string $option): void
    {
        if (\array_key_exists($option, $options) && !\is_bool($options[$option]) && !\is_string($options[$option])) {
            self::warnInvalidRequestOptionType($option, 'bool|string', $options[$option]);
        }
    }

    private static function warnIfPresentAndNotCallable(
        array $options,
        string $option,
        ?string $path = null,
        string $since = '7.11'
    ): void {
        if (\array_key_exists($option, $options) && !\is_callable($options[$option])) {
            self::warnInvalidRequestOptionType($path ?? $option, 'callable', $options[$option], $since);
        }
    }

    private static function warnIfPresentAndNotInt(
        array $options,
        string $option,
        ?string $path = null,
        string $since = '7.11'
    ): void {
        if (\array_key_exists($option, $options) && !\is_int($options[$option])) {
            self::warnInvalidRequestOptionType($path ?? $option, 'int', $options[$option], $since);
        }
    }

    private static function warnIfPresentAndNotNumber(array $options, string $option): void
    {
        if (\array_key_exists($option, $options) && !\is_int($options[$option]) && !\is_float($options[$option])) {
            self::warnInvalidRequestOptionType($option, 'int|float', $options[$option]);
        }
    }

    private static function warnIfPresentAndNotString(array $options, string $option): void
    {
        if (\array_key_exists($option, $options) && !\is_string($options[$option])) {
            self::warnInvalidRequestOptionType($option, 'string', $options[$option]);
        }
    }

    private static function warnIfPresentAndNotStringArray(array $options, string $option, bool $nonEmpty, ?string $path = null): void
    {
        if (!\array_key_exists($option, $options)) {
            return;
        }

        $path = $path ?? $option;
        $expected = ($nonEmpty ? 'non-empty-' : '').'array<array-key, string>';

        if (!\is_array($options[$option]) || ($nonEmpty && $options[$option] === [])) {
            self::warnInvalidRequestOptionType($path, $expected, $options[$option]);

            return;
        }

        foreach ($options[$option] as $index => $item) {
            if (!\is_string($item)) {
                self::warnInvalidRequestOptionType($path.'.'.(string) $index, 'string', $item);

                return;
            }
        }
    }

    /**
     * @param array<array-key, mixed> $options
     */
    private static function warnAboutInvalidProtocolValues(array $options, string $option, ?string $path = null): void
    {
        if (!isset($options[$option]) || !\is_array($options[$option])) {
            return;
        }

        $path = $path ?? $option;

        foreach ($options[$option] as $index => $protocol) {
            if (\is_string($protocol) && $protocol !== 'http' && $protocol !== 'https') {
                self::warnInvalidRequestOptionType($path.'.'.(string) $index, '"http"|"https"', $protocol, '7.13');
            }
        }
    }

    private static function warnIfPresentAndNotStringOrNumber(array $options, string $option): void
    {
        if (
            \array_key_exists($option, $options)
            && !\is_string($options[$option])
            && !\is_int($options[$option])
            && !\is_float($options[$option])
        ) {
            self::warnInvalidRequestOptionType($option, 'string|int|float', $options[$option]);
        }
    }

    /**
     * @param mixed $value
     */
    private static function warnInvalidRequestOptionType(string $option, string $expected, $value, string $since = '7.11'): void
    {
        \trigger_deprecation(
            'guzzlehttp/guzzle',
            $since,
            'Passing %s to request option "%s" is deprecated; guzzlehttp/guzzle 8.0 requires %s.',
            \get_debug_type($value),
            $option,
            $expected
        );
    }

    /**
     * Transfers the given request and applies request options.
     *
     * The URI of the request is not modified and the request options are used
     * as-is without merging in default options.
     *
     * @param array $options See {@see RequestOptions}.
     */
    private function transfer(RequestInterface $request, array $options): PromiseInterface
    {
        $request = $this->applyOptions($request, $options);

        $protocolVersion = $request->getProtocolVersion();

        if ('' === $protocolVersion) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.11', 'Sending a request with an empty protocol version is deprecated; guzzlehttp/guzzle 8.0 will reject empty protocol versions.');

            $request = Psr7\Utils::modifyRequest($request, ['version' => '1.1']);
        } elseif (!self::isProtocolVersionValid($protocolVersion)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.11', 'Sending a request with a malformed protocol version is deprecated; guzzlehttp/guzzle 8.0 will reject malformed protocol versions.');
        }

        /** @var HandlerStack $handler */
        $handler = $options['handler'];

        try {
            return P\Create::promiseFor($handler($request, $options));
        } catch (\Exception $e) {
            return P\Create::rejectionFor($e);
        }
    }

    /**
     * Applies the array of request options to a request.
     */
    private function applyOptions(RequestInterface $request, array &$options): RequestInterface
    {
        $modify = [
            'set_headers' => [],
        ];

        if (isset($options['headers'])) {
            if (array_keys($options['headers']) === range(0, count($options['headers']) - 1)) {
                throw new InvalidArgumentException('The headers array must have header name as keys.');
            }
            $headers = $options['headers'];
            $droppedHeaderNames = self::castDeprecatedHeaderOptionValues($headers);
            if ($droppedHeaderNames !== [] && isset($options['_conditional'])) {
                $options['_conditional'] = Psr7\Utils::caselessRemove($droppedHeaderNames, $options['_conditional']);
            }
            $modify['set_headers'] = $headers;
            unset($options['headers']);
        }

        if (isset($options['form_params'])) {
            if (isset($options['multipart'])) {
                throw new InvalidArgumentException('You cannot use '
                    .'form_params and multipart at the same time. Use the '
                    .'form_params option if you want to send application/'
                    .'x-www-form-urlencoded requests, and the multipart '
                    .'option to send multipart/form-data requests.');
            }
            $options['body'] = \http_build_query(self::normalizeNonFiniteFloats($options['form_params'], 'form_params'), '', '&');
            unset($options['form_params']);
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if (isset($options['multipart'])) {
            $options['body'] = new Psr7\MultipartStream($options['multipart']);
            unset($options['multipart']);
        }

        if (isset($options['json'])) {
            $json = \json_encode($options['json']);
            if (\JSON_ERROR_NONE !== \json_last_error()) {
                throw new InvalidArgumentException('json_encode error: '.\json_last_error_msg());
            }

            /** @var non-empty-string $json */
            $options['body'] = $json;
            unset($options['json']);
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'application/json';
        }

        if (isset($options['decode_content']) && \is_string($options['decode_content'])) {
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = Psr7\Utils::caselessRemove(['Accept-Encoding'], $options['_conditional']);
            $modify['set_headers']['Accept-Encoding'] = (string) $options['decode_content'];
        }

        if (isset($options['body'])) {
            if (\is_array($options['body'])) {
                throw $this->invalidBody();
            }
            $modify['body'] = self::createBodyStream($options['body']);
            unset($options['body']);
        }

        if (!empty($options['auth']) && \is_array($options['auth'])) {
            $value = $options['auth'];
            $type = isset($value[2]) ? Psr7\Utils::asciiToLower($value[2]) : 'basic';
            switch ($type) {
                case 'basic':
                    // Ensure that we don't have the header in different case and set the new value.
                    $modify['set_headers'] = Psr7\Utils::caselessRemove(['Authorization'], $modify['set_headers']);
                    $modify['set_headers']['Authorization'] = 'Basic '
                        .\base64_encode("$value[0]:$value[1]");
                    break;
                case 'digest':
                    // @todo: Do not rely on curl
                    $options['curl'][\CURLOPT_HTTPAUTH] = \CURLAUTH_DIGEST;
                    $options['curl'][\CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
                case 'ntlm':
                    \trigger_deprecation(
                        'guzzlehttp/guzzle',
                        '7.12',
                        'Passing "ntlm" as the built-in auth type is deprecated; guzzlehttp/guzzle 8.0 will no longer apply NTLM through the "auth" request option. NTLM is also deprecated by curl/libcurl and may be unavailable in current or future libcurl builds. Avoid NTLM; if you must use it temporarily, configure cURL HTTP authentication options directly with a libcurl build that still supports NTLM.'
                    );
                    if (!CurlVersion::supportsNtlm()) {
                        throw new InvalidArgumentException('NTLM authentication is not available because the installed curl/libcurl build does not provide NTLM support.');
                    }
                    $options['curl'][\CURLOPT_HTTPAUTH] = \CURLAUTH_NTLM;
                    $options['curl'][\CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
            }
        }

        if (isset($options['query'])) {
            $value = $options['query'];
            if (\is_array($value)) {
                $value = \http_build_query(self::normalizeNonFiniteFloats($value, 'query'), '', '&', \PHP_QUERY_RFC3986);
            }
            if (!\is_string($value)) {
                throw new InvalidArgumentException('query must be a string or array');
            }
            $modify['query'] = $value;
            unset($options['query']);
        }

        // Ensure that sink is not an invalid value.
        if (isset($options['sink'])) {
            // TODO: Add more sink validation?
            if (\is_bool($options['sink'])) {
                throw new InvalidArgumentException('sink must not be a boolean');
            }
        }

        if (isset($options['version'])) {
            $modify['version'] = self::normalizeProtocolVersion($options['version']);
        }

        $request = Psr7\Utils::modifyRequest($request, $modify);
        if ($request->getBody() instanceof Psr7\MultipartStream) {
            // Use a multipart/form-data POST if a Content-Type is not set.
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'multipart/form-data; boundary='
                .$request->getBody()->getBoundary();
        }

        // Merge in conditional headers if they are not present.
        if (isset($options['_conditional'])) {
            // Build up the changes so it's in a single clone of the message.
            $modify = [];
            foreach ($options['_conditional'] as $k => $v) {
                $name = (string) $k;
                if (!$request->hasHeader($name)) {
                    $modify['set_headers'][$name] = $v;
                }
            }
            $request = Psr7\Utils::modifyRequest($request, $modify);
            // Don't pass this internal value along to middleware/handlers.
            unset($options['_conditional']);
        }

        return $request;
    }

    /**
     * @param array<array-key, mixed> $headers
     *
     * @return list<string>
     */
    private static function castDeprecatedHeaderOptionValues(array &$headers): array
    {
        $droppedHeaderNames = [];

        foreach ($headers as $name => $value) {
            if (\is_array($value)) {
                if ($value === []) {
                    $droppedHeaderNames[] = (string) $name;
                    unset($headers[$name]);

                    continue;
                }

                foreach ($value as $index => $item) {
                    if ($item === null || (!\is_string($item) && \is_scalar($item))) {
                        if (\is_float($item) && !\is_finite($item)) {
                            $item = \is_nan($item) ? 'NAN' : ($item > 0 ? 'INF' : '-INF');
                        }
                        $value[$index] = (string) $item;
                    }
                }

                $headers[$name] = $value;

                continue;
            }

            if ($value === null || (!\is_string($value) && \is_scalar($value))) {
                if (\is_float($value) && !\is_finite($value)) {
                    $value = \is_nan($value) ? 'NAN' : ($value > 0 ? 'INF' : '-INF');
                }
                $headers[$name] = (string) $value;
            }
        }

        return $droppedHeaderNames;
    }

    /**
     * @param mixed $body
     */
    private static function createBodyStream($body): StreamInterface
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (\is_resource($body) || $body === null || \is_string($body) || $body instanceof \Iterator) {
            return Psr7\Utils::streamFor($body);
        }

        if (\is_scalar($body)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.12', 'Passing a non-string scalar to the "body" request option is deprecated; guzzlehttp/guzzle 8.0 will reject non-string scalar bodies.');

            return Psr7\Utils::streamFor(self::stringifyScalar($body));
        }

        if (\is_object($body) && \method_exists($body, '__toString')) {
            return Psr7\Utils::streamFor((string) $body);
        }

        if (\is_callable($body)) {
            return Psr7\Utils::streamFor($body);
        }

        throw new InvalidArgumentException(\sprintf(
            'Passing %s to request option "body" is invalid; expected resource|string|null|int|float|bool|StreamInterface|callable&object|Iterator|Stringable.',
            \get_debug_type($body)
        ));
    }

    /**
     * @param bool|float|int|string $value
     */
    private static function stringifyScalar($value): string
    {
        // Normalize non-finite floats to dodge PHP 8.5's (string) NAN
        // coercion warning while the value is still accepted.
        if (\is_float($value) && !\is_finite($value)) {
            $value = \is_nan($value) ? 'NAN' : ($value > 0 ? 'INF' : '-INF');
        }

        return (string) $value;
    }

    /**
     * Converts non-finite floats in the array to the strings PHP coerces
     * them to, as implicit coercion of NAN emits a warning on PHP 8.5.
     */
    private static function normalizeNonFiniteFloats(array $values, string $option): array
    {
        foreach ($values as $key => $value) {
            if (\is_array($value)) {
                $values[$key] = self::normalizeNonFiniteFloats($value, $option);
            } elseif (\is_float($value) && !\is_finite($value)) {
                \trigger_deprecation('guzzlehttp/guzzle', '7.12', 'Passing a non-finite float in the "%s" request option is deprecated; guzzlehttp/guzzle 8.0 will reject non-finite floats.', $option);

                $values[$key] = \is_nan($value) ? 'NAN' : ($value > 0 ? 'INF' : '-INF');
            }
        }

        return $values;
    }

    /**
     * @param string|int|float $version
     */
    private static function normalizeProtocolVersion($version): string
    {
        if ('' === $version) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.11', 'Passing an empty "version" request option is deprecated; guzzlehttp/guzzle 8.0 will reject empty protocol versions.');

            return '1.1';
        }

        return \is_float($version) ? \number_format($version, 1, '.', '') : (string) $version;
    }

    private static function isProtocolVersionValid(string $version): bool
    {
        return 1 === \preg_match('/^\d+(?:\.\d+)?$/D', $version);
    }

    /**
     * Return an InvalidArgumentException with pre-set message.
     */
    private function invalidBody(): InvalidArgumentException
    {
        return new InvalidArgumentException('Passing in the "body" request '
            .'option as an array to send a request is not supported. '
            .'Please use the "form_params" request option to send a '
            .'application/x-www-form-urlencoded request, or the "multipart" '
            .'request option to send a multipart/form-data request.');
    }
}
