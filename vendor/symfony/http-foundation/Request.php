<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// Help opcache.preload discover always-needed symbols
class_exists(AcceptHeader::class);
class_exists(FileBag::class);
class_exists(HeaderBag::class);
class_exists(HeaderUtils::class);
class_exists(InputBag::class);
class_exists(ParameterBag::class);
class_exists(ServerBag::class);

/**
 * Request represents an HTTP request.
 *
 * The methods dealing with URL accept / return a raw path (% encoded):
 *   * getBasePath
 *   * getBaseUrl
 *   * getPathInfo
 *   * getRequestUri
 *   * getUri
 *   * getUriForPath
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Request
{
    public const HEADER_FORWARDED = 0b000001; // When using RFC 7239
    public const HEADER_X_FORWARDED_FOR = 0b000010;
    public const HEADER_X_FORWARDED_HOST = 0b000100;
    public const HEADER_X_FORWARDED_PROTO = 0b001000;
    public const HEADER_X_FORWARDED_PORT = 0b010000;
    public const HEADER_X_FORWARDED_PREFIX = 0b100000;

    public const HEADER_X_FORWARDED_AWS_ELB = 0b0011010; // AWS ELB doesn't send X-Forwarded-Host
    public const HEADER_X_FORWARDED_TRAEFIK = 0b0111110; // All "X-Forwarded-*" headers sent by Traefik reverse proxy

    public const METHOD_HEAD = 'HEAD';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PURGE = 'PURGE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_TRACE = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';
    public const METHOD_QUERY = 'QUERY';

    /**
     * @var string[]
     */
    protected static array $trustedProxies = [];

    /**
     * @var string[]
     */
    protected static array $trustedHostPatterns = [];

    /**
     * @var string[]
     */
    protected static array $trustedHosts = [];

    protected static bool $httpMethodParameterOverride = false;

    /**
     * The HTTP methods that can be overridden.
     *
     * @var uppercase-string[]|null
     */
    protected static ?array $allowedHttpMethodOverride = null;

    /**
     * Custom parameters.
     */
    public ParameterBag $attributes;

    /**
     * Request body parameters ($_POST).
     *
     * @see getPayload() for portability between content types
     */
    public InputBag $request;

    /**
     * Query string parameters ($_GET).
     *
     * @var InputBag<string>
     */
    public InputBag $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     */
    public ServerBag $server;

    /**
     * Uploaded files ($_FILES).
     */
    public FileBag $files;

    /**
     * Cookies ($_COOKIE).
     *
     * @var InputBag<string>
     */
    public InputBag $cookies;

    /**
     * Headers (taken from the $_SERVER).
     */
    public HeaderBag $headers;

    /**
     * @var string|resource|false|null
     */
    protected $content;

    /**
     * @var string[]|null
     */
    protected ?array $languages = null;

    /**
     * @var string[]|null
     */
    protected ?array $charsets = null;

    /**
     * @var string[]|null
     */
    protected ?array $encodings = null;

    /**
     * @var string[]|null
     */
    protected ?array $acceptableContentTypes = null;

    protected ?string $pathInfo = null;
    protected ?string $requestUri = null;
    protected ?string $baseUrl = null;
    protected ?string $basePath = null;
    protected ?string $method = null;
    protected ?string $format = null;
    protected SessionInterface|\Closure|null $session = null;
    protected ?string $locale = null;
    protected string $defaultLocale = 'en';

    /**
     * @var array<string, string[]>|null
     */
    protected static ?array $formats = null;

    protected static ?\Closure $requestFactory = null;

    private ?string $preferredFormat = null;

    private bool $isHostValid = true;
    private bool $isForwardedValid = true;
    private bool $isSafeContentPreferred;

    private array $trustedValuesCache = [];

    private static int $trustedHeaderSet = -1;

    private const FORWARDED_PARAMS = [
        self::HEADER_X_FORWARDED_FOR => 'for',
        self::HEADER_X_FORWARDED_HOST => 'host',
        self::HEADER_X_FORWARDED_PROTO => 'proto',
        self::HEADER_X_FORWARDED_PORT => 'host',
    ];

    /**
     * Names for headers that can be trusted when
     * using trusted proxies.
     *
     * The FORWARDED header is the standard as of rfc7239.
     *
     * The other headers are non-standard, but widely used
     * by popular reverse proxies (like Apache mod_proxy or Amazon EC2).
     */
    private const TRUSTED_HEADERS = [
        self::HEADER_FORWARDED => 'FORWARDED',
        self::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR',
        self::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST',
        self::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO',
        self::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT',
        self::HEADER_X_FORWARDED_PREFIX => 'X_FORWARDED_PREFIX',
    ];

    /**
     * This mapping is used when no exact MIME match is found in $formats.
     *
     * It enables mappings like application/soap+xml -> xml.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6839
     * @see https://datatracker.ietf.org/doc/html/rfc7303
     * @see https://www.iana.org/assignments/media-types/media-types.xhtml
     */
    private const STRUCTURED_SUFFIX_FORMATS = [
        'json' => 'json',
        'xml' => 'xml',
        'xhtml' => 'html',
        'cbor' => 'cbor',
        'zip' => 'zip',
        'ber' => 'asn1',
        'der' => 'asn1',
        'tlv' => 'tlv',
        'wbxml' => 'xml',
        'yaml' => 'yaml',
    ];

    private bool $isIisRewrite = false;

    /**
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param string|resource|null $content    The raw body data
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param string|resource|null $content    The raw body data
     */
    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): void
    {
        $this->request = new InputBag($request);
        $this->query = new InputBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new InputBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $this->content = $content;
        $this->languages = null;
        $this->charsets = null;
        $this->encodings = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }

    /**
     * Creates a new request with values from PHP's super globals.
     */
    public static function createFromGlobals(): static
    {
        if (!\in_array($_SERVER['REQUEST_METHOD'] ?? null, ['PUT', 'DELETE', 'PATCH', 'QUERY'], true)) {
            return self::createRequestFromFactory($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
        }

        if (\PHP_VERSION_ID < 80400) {
            if (!isset($_SERVER['CONTENT_TYPE']) || str_starts_with($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded')) {
                $content = file_get_contents('php://input');
                parse_str($content, $post);
            } else {
                $content = null;
                $post = $_POST;
            }

            return self::createRequestFromFactory($_GET, $post, [], $_COOKIE, $_FILES, $_SERVER, $content);
        }

        try {
            [$post, $files] = request_parse_body();
        } catch (\RequestParseBodyException) {
            $post = $_POST;
            $files = $_FILES;
        }

        return self::createRequestFromFactory($_GET, $post, [], $_COOKIE, $files, $_SERVER);
    }

    /**
     * Creates a Request based on a given URI and configuration.
     *
     * The information contained in the URI always take precedence
     * over the other information (server and parameters).
     *
     * @param string               $uri        The URI
     * @param string               $method     The HTTP method
     * @param array                $parameters The query (GET) or request (POST) parameters
     * @param array                $cookies    The request cookies ($_COOKIE)
     * @param array                $files      The request files ($_FILES)
     * @param array                $server     The server parameters ($_SERVER)
     * @param string|resource|null $content    The raw body data
     *
     * @throws BadRequestException When the URI is invalid
     */
    public static function create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = null): static
    {
        $server = array_replace([
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Symfony',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR' => '127.0.0.1',
            'SCRIPT_NAME' => '',
            'SCRIPT_FILENAME' => '',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
        ], $server);

        $server['PATH_INFO'] = '';
        $server['REQUEST_METHOD'] = strtoupper($method);

        if (($i = strcspn($uri, ':/?#')) && ':' === ($uri[$i] ?? null) && (strspn($uri, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-.') !== $i || strcspn($uri, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'))) {
            throw new BadRequestException('Invalid URI: Scheme is malformed.');
        }
        if (false === $components = parse_url(\strlen($uri) !== strcspn($uri, '?#') ? $uri : $uri.'#')) {
            throw new BadRequestException('Invalid URI.');
        }

        $part = ($components['user'] ?? '').':'.($components['pass'] ?? '');

        if (':' !== $part && \strlen($part) !== strcspn($part, '[]')) {
            throw new BadRequestException('Invalid URI: Userinfo is malformed.');
        }
        if (($part = $components['host'] ?? '') && !self::isHostValid($part)) {
            throw new BadRequestException('Invalid URI: Host is malformed.');
        }
        if (false !== ($i = strpos($uri, '\\')) && $i < strcspn($uri, '?#')) {
            throw new BadRequestException('Invalid URI: A URI cannot contain a backslash.');
        }
        if (\strlen($uri) !== strcspn($uri, "\r\n\t")) {
            throw new BadRequestException('Invalid URI: A URI cannot contain CR/LF/TAB characters.');
        }
        if ('' !== $uri && (\ord($uri[0]) <= 32 || \ord($uri[-1]) <= 32)) {
            throw new BadRequestException('Invalid URI: A URI must not start nor end with ASCII control characters or spaces.');
        }

        if (isset($components['host'])) {
            $server['SERVER_NAME'] = $components['host'];
            $server['HTTP_HOST'] = $components['host'];
        }

        if (isset($components['scheme'])) {
            if ('https' === $components['scheme']) {
                $server['HTTPS'] = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['HTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }

        if (isset($components['port'])) {
            $server['SERVER_PORT'] = $components['port'];
            $server['HTTP_HOST'] .= ':'.$components['port'];
        }

        if (isset($components['user'])) {
            $server['PHP_AUTH_USER'] = $components['user'];
        }

        if (isset($components['pass'])) {
            $server['PHP_AUTH_PW'] = $components['pass'];
        }

        if ('' === $path = $components['path'] ?? '') {
            $components['path'] = '/';
        } elseif (!isset($components['scheme']) && !isset($components['host']) && '/' !== $path[0]) {
            if (false !== $pos = strpos($path, '/')) {
                $path = substr($path, 0, $pos);
            }

            if (str_contains($path, ':')) {
                throw new BadRequestException('Invalid URI: Path is malformed.');
            }
        }

        switch (strtoupper($method)) {
            case 'POST':
            case 'PUT':
            case 'DELETE':
            case 'QUERY':
                if (!isset($server['CONTENT_TYPE'])) {
                    $server['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
                }
                // no break
            case 'PATCH':
                $request = $parameters;
                $query = [];
                break;
            default:
                $request = [];
                $query = $parameters;
                break;
        }

        $queryString = '';
        if (isset($components['query'])) {
            parse_str(html_entity_decode($components['query']), $qs);

            if ($query) {
                $query = array_replace($qs, $query);
                $queryString = http_build_query($query, '', '&');
            } else {
                $query = $qs;
                $queryString = $components['query'];
            }
        } elseif ($query) {
            $queryString = http_build_query($query, '', '&');
        }

        $server['REQUEST_URI'] = $components['path'].('' !== $queryString ? '?'.$queryString : '');
        $server['QUERY_STRING'] = $queryString;

        return self::createRequestFromFactory($query, $request, [], $cookies, $files, $server, $content);
    }

    /**
     * Sets a callable able to create a Request instance.
     *
     * This is mainly useful when you need to override the Request class
     * to keep BC with an existing system. It should not be used for any
     * other purpose.
     */
    public static function setFactory(?callable $callable): void
    {
        self::$requestFactory = null === $callable ? null : $callable(...);
    }

    /**
     * Clones a request and overrides some of its parameters.
     *
     * @param array|null $query      The GET parameters
     * @param array|null $request    The POST parameters
     * @param array|null $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array|null $cookies    The COOKIE parameters
     * @param array|null $files      The FILES parameters
     * @param array|null $server     The SERVER parameters
     */
    public function duplicate(?array $query = null, ?array $request = null, ?array $attributes = null, ?array $cookies = null, ?array $files = null, ?array $server = null): static
    {
        $dup = clone $this;
        if (null !== $query) {
            $dup->query = new InputBag($query);
        }
        if (null !== $request) {
            $dup->request = new InputBag($request);
        }
        if (null !== $attributes) {
            $dup->attributes = new ParameterBag($attributes);
        }
        if (null !== $cookies) {
            $dup->cookies = new InputBag($cookies);
        }
        if (null !== $files) {
            $dup->files = new FileBag($files);
        }
        if (null !== $server) {
            $dup->server = new ServerBag($server);
            $dup->headers = new HeaderBag($dup->server->getHeaders());
        }
        $dup->languages = null;
        $dup->charsets = null;
        $dup->encodings = null;
        $dup->acceptableContentTypes = null;
        $dup->pathInfo = null;
        $dup->requestUri = null;
        $dup->baseUrl = null;
        $dup->basePath = null;
        $dup->method = null;
        $dup->format = null;

        if (!$dup->attributes->has('_format') && $this->attributes->has('_format')) {
            $dup->attributes->set('_format', $this->attributes->get('_format'));
        }

        if (!$dup->getRequestFormat(null)) {
            $dup->setRequestFormat($this->getRequestFormat(null));
        }

        return $dup;
    }

    /**
     * Clones the current request.
     *
     * Note that the session is not cloned as duplicated requests
     * are most of the time sub-requests of the main one.
     */
    public function __clone()
    {
        $this->query = clone $this->query;
        $this->request = clone $this->request;
        $this->attributes = clone $this->attributes;
        $this->cookies = clone $this->cookies;
        $this->files = clone $this->files;
        $this->server = clone $this->server;
        $this->headers = clone $this->headers;
    }

    public function __toString(): string
    {
        $content = $this->getContent();

        $cookieHeader = '';
        $cookies = [];

        foreach ($this->cookies as $k => $v) {
            $cookies[] = \is_array($v) ? http_build_query([$k => $v], '', '; ', \PHP_QUERY_RFC3986) : "$k=$v";
        }

        if ($cookies) {
            $cookieHeader = 'Cookie: '.implode('; ', $cookies)."\r\n";
        }

        return
            \sprintf('%s %s %s', $this->getMethod(), $this->getRequestUri(), $this->server->get('SERVER_PROTOCOL'))."\r\n".
            $this->headers.
            $cookieHeader."\r\n".
            $content;
    }

    /**
     * Overrides the PHP global variables according to this request instance.
     *
     * It overrides $_GET, $_POST, $_REQUEST, $_SERVER, $_COOKIE.
     * $_FILES is never overridden, see rfc1867
     */
    public function overrideGlobals(): void
    {
        $this->server->set('QUERY_STRING', static::normalizeQueryString(http_build_query($this->query->all(), '', '&')));

        $_GET = $this->query->all();
        $_POST = $this->request->all();
        $_SERVER = $this->server->all();
        $_COOKIE = $this->cookies->all();

        foreach ($this->headers->all() as $key => $value) {
            $key = strtoupper(str_replace('-', '_', $key));
            if (\in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $_SERVER[$key] = implode(', ', $value);
            } else {
                $_SERVER['HTTP_'.$key] = implode(', ', $value);
            }
        }

        $request = ['g' => $_GET, 'p' => $_POST, 'c' => $_COOKIE];

        $requestOrder = \ini_get('request_order') ?: \ini_get('variables_order');
        $requestOrder = preg_replace('#[^cgp]#', '', strtolower($requestOrder)) ?: 'gp';

        $_REQUEST = [[]];

        foreach (str_split($requestOrder) as $order) {
            $_REQUEST[] = $request[$order];
        }

        $_REQUEST = array_merge(...$_REQUEST);
    }

    /**
     * Sets a list of trusted proxies.
     *
     * You should only list the reverse proxies that you manage directly.
     *
     * @param array                          $proxies          A list of trusted proxies, the string 'REMOTE_ADDR' will be replaced with $_SERVER['REMOTE_ADDR'] and 'PRIVATE_SUBNETS' by IpUtils::PRIVATE_SUBNETS
     * @param int-mask-of<Request::HEADER_*> $trustedHeaderSet A bit field to set which headers to trust from your proxies
     */
    public static function setTrustedProxies(array $proxies, int $trustedHeaderSet): void
    {
        if (false !== $i = array_search('REMOTE_ADDR', $proxies, true)) {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $proxies[$i] = $_SERVER['REMOTE_ADDR'];
            } else {
                unset($proxies[$i]);
                $proxies = array_values($proxies);
            }
        }

        if (false !== ($i = array_search('PRIVATE_SUBNETS', $proxies, true)) || false !== ($i = array_search('private_ranges', $proxies, true))) {
            unset($proxies[$i]);
            $proxies = array_merge($proxies, IpUtils::PRIVATE_SUBNETS);
        }

        self::$trustedProxies = $proxies;
        self::$trustedHeaderSet = $trustedHeaderSet;
    }

    /**
     * Gets the list of trusted proxies.
     *
     * @return string[]
     */
    public static function getTrustedProxies(): array
    {
        return self::$trustedProxies;
    }

    /**
     * Gets the set of trusted headers from trusted proxies.
     *
     * @return int A bit field of Request::HEADER_* that defines which headers are trusted from your proxies
     */
    public static function getTrustedHeaderSet(): int
    {
        return self::$trustedHeaderSet;
    }

    /**
     * Sets a list of trusted host patterns.
     *
     * You should only list the hosts you manage using regexs.
     *
     * @param array $hostPatterns A list of trusted host patterns
     */
    public static function setTrustedHosts(array $hostPatterns): void
    {
        self::$trustedHostPatterns = array_map(static fn ($hostPattern) => \sprintf('{%s}i', $hostPattern), $hostPatterns);
        // we need to reset trusted hosts on trusted host patterns change
        self::$trustedHosts = [];
    }

    /**
     * Gets the list of trusted host patterns.
     *
     * @return string[]
     */
    public static function getTrustedHosts(): array
    {
        return self::$trustedHostPatterns;
    }

    /**
     * Normalizes a query string.
     *
     * It builds a normalized query string, where keys/value pairs are alphabetized,
     * have consistent escaping and unneeded delimiters are removed.
     */
    public static function normalizeQueryString(?string $qs): string
    {
        if ('' === ($qs ?? '')) {
            return '';
        }

        $qs = HeaderUtils::parseQuery($qs);
        ksort($qs);

        return http_build_query($qs, '', '&', \PHP_QUERY_RFC3986);
    }

    /**
     * Enables support for the _method request parameter to determine the intended HTTP method.
     *
     * Be warned that enabling this feature might lead to CSRF issues in your code.
     * Check that you are using CSRF tokens when required.
     * If the HTTP method parameter override is enabled, an html-form with method "POST" can be altered
     * and used to send a "PUT" or "DELETE" request via the _method request parameter.
     * If these methods are not protected against CSRF, this presents a possible vulnerability.
     *
     * The HTTP method can only be overridden when the real HTTP method is POST.
     */
    public static function enableHttpMethodParameterOverride(): void
    {
        self::$httpMethodParameterOverride = true;
    }

    /**
     * Checks whether support for the _method request parameter is enabled.
     */
    public static function getHttpMethodParameterOverride(): bool
    {
        return self::$httpMethodParameterOverride;
    }

    /**
     * Sets the list of HTTP methods that can be overridden.
     *
     * Set to null to allow all methods to be overridden (default). Set to an
     * empty array to disallow overrides entirely. Otherwise, provide the list
     * of uppercased method names that are allowed.
     *
     * @param uppercase-string[]|null $methods
     */
    public static function setAllowedHttpMethodOverride(?array $methods): void
    {
        if (array_intersect($methods ?? [], ['GET', 'HEAD', 'CONNECT', 'TRACE'])) {
            throw new \InvalidArgumentException('The HTTP methods "GET", "HEAD", "CONNECT", and "TRACE" cannot be overridden.');
        }

        self::$allowedHttpMethodOverride = $methods;
    }

    /**
     * Gets the list of HTTP methods that can be overridden.
     *
     * @return uppercase-string[]|null
     */
    public static function getAllowedHttpMethodOverride(): ?array
    {
        return self::$allowedHttpMethodOverride;
    }

    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
     * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
     * public property instead (attributes, query, request).
     *
     * Order of precedence: PATH (routing placeholders or custom attributes), GET, POST
     *
     * @deprecated since Symfony 7.4, use properties `->attributes`, `query` or `request` directly instead
     */
    public function get(string $key, mixed $default = null): mixed
    {
        trigger_deprecation('symfony/http-foundation', '7.4', 'Request::get() is deprecated, use properties ->attributes, query or request directly instead.');

        if ($this !== $result = $this->attributes->get($key, $this)) {
            return $result;
        }

        if ($this->query->has($key)) {
            return $this->query->all()[$key];
        }

        if ($this->request->has($key)) {
            return $this->request->all()[$key];
        }

        return $default;
    }

    /**
     * Gets the Session.
     *
     * @throws SessionNotFoundException When session is not set properly
     */
    public function getSession(): SessionInterface
    {
        $session = $this->session;
        if (!$session instanceof SessionInterface && null !== $session) {
            $this->setSession($session = $session());
        }

        if (null === $session) {
            throw new SessionNotFoundException('Session has not been set.');
        }

        return $session;
    }

    /**
     * Whether the request contains a Session which was started in one of the
     * previous requests.
     */
    public function hasPreviousSession(): bool
    {
        // the check for $this->session avoids malicious users trying to fake a session cookie with proper name
        return $this->hasSession() && $this->cookies->has($this->getSession()->getName());
    }

    /**
     * Whether the request contains a Session object.
     *
     * This method does not give any information about the state of the session object,
     * like whether the session is started or not. It is just a way to check if this Request
     * is associated with a Session instance.
     *
     * @param bool $skipIfUninitialized When true, ignores factories injected by `setSessionFactory`
     */
    public function hasSession(bool $skipIfUninitialized = false): bool
    {
        return null !== $this->session && (!$skipIfUninitialized || $this->session instanceof SessionInterface);
    }

    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    /**
     * @internal
     *
     * @param callable(): SessionInterface $factory
     */
    public function setSessionFactory(callable $factory): void
    {
        $this->session = $factory(...);
    }

    /**
     * Returns the client IP addresses.
     *
     * In the returned array the most trusted IP address is first, and the
     * least trusted one last. The "real" client IP address is the last one,
     * but this is also the least trusted one. Trusted proxies are stripped.
     *
     * Use this method carefully; you should use getClientIp() instead.
     *
     * @see getClientIp()
     */
    public function getClientIps(): array
    {
        $ip = $this->server->get('REMOTE_ADDR');

        if (!$this->isFromTrustedProxy()) {
            return [$ip];
        }

        return $this->getTrustedValues(self::HEADER_X_FORWARDED_FOR, $ip) ?: [$ip];
    }

    /**
     * Returns the client IP address.
     *
     * This method can read the client IP address from the "X-Forwarded-For" header
     * when trusted proxies were set via "setTrustedProxies()". The "X-Forwarded-For"
     * header value is a comma+space separated list of IP addresses, the left-most
     * being the original client, and each successive proxy that passed the request
     * adding the IP address where it received the request from.
     *
     * @see getClientIps()
     * @see https://wikipedia.org/wiki/X-Forwarded-For
     */
    public function getClientIp(): ?string
    {
        return $this->getClientIps()[0];
    }

    /**
     * Returns current script name.
     */
    public function getScriptName(): string
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }

    /**
     * Returns the path being requested relative to the executed script.
     *
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns '/'
     *  * http://localhost/mysite/about        returns '/about'
     *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string The raw path (i.e. not urldecoded)
     */
    public function getPathInfo(): string
    {
        return $this->pathInfo ??= $this->preparePathInfo();
    }

    /**
     * Returns the root path from which this request is executed.
     *
     * Suppose that an index.php file instantiates this request object:
     *
     *  * http://localhost/index.php         returns an empty string
     *  * http://localhost/index.php/page    returns an empty string
     *  * http://localhost/web/index.php     returns '/web'
     *  * http://localhost/we%20b/index.php  returns '/we%20b'
     *
     * @return string The raw path (i.e. not urldecoded)
     */
    public function getBasePath(): string
    {
        return $this->basePath ??= $this->prepareBasePath();
    }

    /**
     * Returns the root URL from which this request is executed.
     *
     * The base URL never ends with a /.
     *
     * This is similar to getBasePath(), except that it also includes the
     * script filename (e.g. index.php) if one exists.
     *
     * @return string The raw URL (i.e. not urldecoded)
     */
    public function getBaseUrl(): string
    {
        $trustedPrefix = '';

        // the proxy prefix must be prepended to any prefix being needed at the webserver level
        if ($this->isFromTrustedProxy() && $trustedPrefixValues = $this->getTrustedValues(self::HEADER_X_FORWARDED_PREFIX)) {
            $trustedPrefix = rtrim($trustedPrefixValues[0], '/');
        }

        return $trustedPrefix.$this->getBaseUrlReal();
    }

    /**
     * Returns the real base URL received by the webserver from which this request is executed.
     * The URL does not include trusted reverse proxy prefix.
     *
     * @return string The raw URL (i.e. not urldecoded)
     */
    private function getBaseUrlReal(): string
    {
        return $this->baseUrl ??= $this->prepareBaseUrl();
    }

    /**
     * Gets the request's scheme.
     */
    public function getScheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * Returns the port on which the request is made.
     *
     * This method can read the client port from the "X-Forwarded-Port" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Port" header must contain the client port.
     *
     * @return int|string|null Can be a string if fetched from the server bag
     */
    public function getPort(): int|string|null
    {
        if ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_X_FORWARDED_PORT)) {
            $host = $host[0];
        } elseif ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_X_FORWARDED_HOST)) {
            $host = $host[0];
        } elseif (!$host = $this->headers->get('HOST')) {
            return $this->server->get('SERVER_PORT');
        }

        if ('[' === $host[0]) {
            $pos = strpos($host, ':', strrpos($host, ']'));
        } else {
            $pos = strrpos($host, ':');
        }

        if (false !== $pos && $port = substr($host, $pos + 1)) {
            return (int) $port;
        }

        return 'https' === $this->getScheme() ? 443 : 80;
    }

    /**
     * Returns the user.
     */
    public function getUser(): ?string
    {
        return $this->headers->get('PHP_AUTH_USER');
    }

    /**
     * Returns the password.
     */
    public function getPassword(): ?string
    {
        return $this->headers->get('PHP_AUTH_PW');
    }

    /**
     * Gets the user info.
     *
     * @return string|null A user name if any and, optionally, scheme-specific information about how to gain authorization to access the server
     */
    public function getUserInfo(): ?string
    {
        $userinfo = $this->getUser();

        $pass = $this->getPassword();
        if ('' != $pass) {
            $userinfo .= ":$pass";
        }

        return $userinfo;
    }

    /**
     * Returns the HTTP host being requested.
     *
     * The port name will be appended to the host if it's non-standard.
     */
    public function getHttpHost(): string
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();

        if (('http' === $scheme && 80 == $port) || ('https' === $scheme && 443 == $port)) {
            return $this->getHost();
        }

        return $this->getHost().':'.$port;
    }

    /**
     * Returns the requested URI (path and query string).
     *
     * @return string The raw URI (i.e. not URI decoded)
     */
    public function getRequestUri(): string
    {
        return $this->requestUri ??= $this->prepareRequestUri();
    }

    /**
     * Gets the scheme and HTTP host.
     *
     * If the URL was called with basic authentication, the user
     * and the password are not added to the generated string.
     */
    public function getSchemeAndHttpHost(): string
    {
        return $this->getScheme().'://'.$this->getHttpHost();
    }

    /**
     * Generates a normalized URI (URL) for the Request.
     *
     * @see getQueryString()
     */
    public function getUri(): string
    {
        if (null !== $qs = $this->getQueryString()) {
            $qs = '?'.$qs;
        }

        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs;
    }

    /**
     * Generates a normalized URI for the given path.
     *
     * @param string $path A path to use instead of the current one
     */
    public function getUriForPath(string $path): string
    {
        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$path;
    }

    /**
     * Returns the path as relative reference from the current Request path.
     *
     * Only the URIs path component (no schema, host etc.) is relevant and must be given.
     * Both paths must be absolute and not contain relative parts.
     * Relative URLs from one resource to another are useful when generating self-contained downloadable document archives.
     * Furthermore, they can be used to reduce the link size in documents.
     *
     * Example target paths, given a base path of "/a/b/c/d":
     * - "/a/b/c/d"     -> ""
     * - "/a/b/c/"      -> "./"
     * - "/a/b/"        -> "../"
     * - "/a/b/c/other" -> "other"
     * - "/a/x/y"       -> "../../x/y"
     */
    public function getRelativeUriForPath(string $path): string
    {
        // be sure that we are dealing with an absolute path
        if (!isset($path[0]) || '/' !== $path[0]) {
            return $path;
        }

        if ($path === $basePath = $this->getPathInfo()) {
            return '';
        }

        $sourceDirs = explode('/', isset($basePath[0]) && '/' === $basePath[0] ? substr($basePath, 1) : $basePath);
        $targetDirs = explode('/', substr($path, 1));
        array_pop($sourceDirs);
        $targetFile = array_pop($targetDirs);

        foreach ($sourceDirs as $i => $dir) {
            if (isset($targetDirs[$i]) && $dir === $targetDirs[$i]) {
                unset($sourceDirs[$i], $targetDirs[$i]);
            } else {
                break;
            }
        }

        $targetDirs[] = $targetFile;
        $path = str_repeat('../', \count($sourceDirs)).implode('/', $targetDirs);

        // A reference to the same base directory or an empty subdirectory must be prefixed with "./".
        // This also applies to a segment with a colon character (e.g., "file:colon") that cannot be used
        // as the first segment of a relative-path reference, as it would be mistaken for a scheme name
        // (see https://tools.ietf.org/html/rfc3986#section-4.2).
        return !isset($path[0]) || '/' === $path[0]
            || false !== ($colonPos = strpos($path, ':')) && ($colonPos < ($slashPos = strpos($path, '/')) || false === $slashPos)
            ? "./$path" : $path;
    }

    /**
     * Generates the normalized query string for the Request.
     *
     * It builds a normalized query string, where keys/value pairs are alphabetized
     * and have consistent escaping.
     */
    public function getQueryString(): ?string
    {
        $qs = static::normalizeQueryString($this->server->get('QUERY_STRING'));

        return '' === $qs ? null : $qs;
    }

    /**
     * Checks whether the request is secure or not.
     *
     * This method can read the client protocol from the "X-Forwarded-Proto" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
     */
    public function isSecure(): bool
    {
        if ($this->isFromTrustedProxy() && $proto = $this->getTrustedValues(self::HEADER_X_FORWARDED_PROTO)) {
            return \in_array(strtolower($proto[0]), ['https', 'on', 'ssl', '1'], true);
        }

        $https = $this->server->get('HTTPS');

        return $https && (!\is_string($https) || 'off' !== strtolower($https));
    }

    /**
     * Returns the host name.
     *
     * This method can read the client host name from the "X-Forwarded-Host" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Host" header must contain the client host name.
     *
     * @throws SuspiciousOperationException when the host name is invalid or not trusted
     */
    public function getHost(): string
    {
        if ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_X_FORWARDED_HOST)) {
            $host = $host[0];
        } else {
            $host = $this->headers->get('HOST') ?: $this->server->get('SERVER_NAME') ?: $this->server->get('SERVER_ADDR', '');
        }

        // trim and remove port number from host
        // host is lowercase as per RFC 952/2181
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        // the host can come from the user (HTTP_HOST and depending on the configuration, SERVER_NAME too can come from the user)
        if ($host && !self::isHostValid($host)) {
            if (!$this->isHostValid) {
                return '';
            }
            $this->isHostValid = false;

            throw new SuspiciousOperationException(\sprintf('Invalid Host "%s".', $host));
        }

        if (\count(self::$trustedHostPatterns) > 0) {
            // to avoid host header injection attacks, you should provide a list of trusted host patterns

            if (\in_array($host, self::$trustedHosts, true)) {
                return $host;
            }

            foreach (self::$trustedHostPatterns as $pattern) {
                if (preg_match($pattern, $host)) {
                    self::$trustedHosts[] = $host;

                    return $host;
                }
            }

            if (!$this->isHostValid) {
                return '';
            }
            $this->isHostValid = false;

            throw new SuspiciousOperationException(\sprintf('Untrusted Host "%s".', $host));
        }

        return $host;
    }

    /**
     * Sets the request method.
     */
    public function setMethod(string $method): void
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', $method);
    }

    /**
     * Gets the request "intended" method.
     *
     * If the X-HTTP-Method-Override header is set, and if the method is a POST,
     * then it is used to determine the "real" intended HTTP method.
     *
     * The _method request parameter can also be used to determine the HTTP method,
     * but only if enableHttpMethodParameterOverride() has been called.
     *
     * The method is always an uppercased string.
     *
     * @see getRealMethod()
     */
    public function getMethod(): string
    {
        if (null !== $this->method) {
            return $this->method;
        }

        $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));

        if ('POST' !== $this->method || !(self::$allowedHttpMethodOverride ?? true)) {
            return $this->method;
        }

        $method = $this->headers->get('X-HTTP-METHOD-OVERRIDE');

        if (!$method && self::$httpMethodParameterOverride) {
            $method = $this->request->get('_method', $this->query->get('_method', 'POST'));
        }

        if (!\is_string($method)) {
            return $this->method;
        }

        $method = strtoupper($method);

        if (\in_array($method, ['GET', 'HEAD', 'CONNECT', 'TRACE'], true)) {
            trigger_deprecation('symfony/http-foundation', '7.4', 'HTTP method override is deprecated for methods GET, HEAD, CONNECT and TRACE; it will be ignored in Symfony 8.0.', $method);
        }

        if (self::$allowedHttpMethodOverride && !\in_array($method, self::$allowedHttpMethodOverride, true)) {
            return $this->method;
        }

        if (\strlen($method) !== strspn($method, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')) {
            throw new SuspiciousOperationException('Invalid HTTP method override.');
        }

        return $this->method = $method;
    }

    /**
     * Gets the "real" request method.
     *
     * @see getMethod()
     */
    public function getRealMethod(): string
    {
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    /**
     * Gets the mime type associated with the format.
     */
    public function getMimeType(string $format): ?string
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }

    /**
     * Gets the mime types associated with the format.
     *
     * @return string[]
     */
    public static function getMimeTypes(string $format): array
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        return static::$formats[$format] ?? [];
    }

    /**
     * Gets the format associated with the mime type.
     *
     *  Resolution order:
     *   1) Exact match on the full MIME type (e.g. "application/json").
     *   2) Match on the canonical MIME type (i.e. before the first ";" parameter).
     *   3) If the type is "application/*+suffix", use the structured syntax suffix
     *      mapping (e.g. "application/foo+json" → "json"), when available.
     *   4) If $subtypeFallback is true and no match was found:
     *      - return the MIME subtype (without "x-" prefix), provided it does not
     *        contain a "+" (e.g. "application/x-yaml" → "yaml", "text/csv" → "csv").
     *
     * @param string|null $mimeType        The mime type to check
     * @param bool        $subtypeFallback Whether to fall back to the subtype if no exact match is found
     */
    public function getFormat(?string $mimeType/* , bool $subtypeFallback = false */): ?string
    {
        $subtypeFallback = 2 <= \func_num_args() ? func_get_arg(1) : false;
        $canonicalMimeType = null;
        if ($mimeType && false !== $pos = strpos($mimeType, ';')) {
            $canonicalMimeType = trim(substr($mimeType, 0, $pos));
        }

        if (null === static::$formats) {
            static::initializeFormats();
        }

        $exactFormat = null;
        $canonicalFormat = null;

        foreach (static::$formats as $format => $mimeTypes) {
            if (\in_array($mimeType, $mimeTypes, true)) {
                $exactFormat = $format;
            }
            if (null !== $canonicalMimeType && \in_array($canonicalMimeType, $mimeTypes, true)) {
                $canonicalFormat = $format;
            }
        }

        if ($format = $exactFormat ?? $canonicalFormat) {
            return $format;
        }

        if (!$canonicalMimeType ??= $mimeType) {
            return null;
        }

        if (str_starts_with($canonicalMimeType, 'application/') && str_contains($canonicalMimeType, '+')) {
            $suffix = substr(strrchr($canonicalMimeType, '+'), 1);
            if (isset(self::STRUCTURED_SUFFIX_FORMATS[$suffix])) {
                return self::STRUCTURED_SUFFIX_FORMATS[$suffix];
            }
        }

        if ($subtypeFallback && str_contains($canonicalMimeType, '/')) {
            [, $subtype] = explode('/', $canonicalMimeType, 2);
            if (str_starts_with($subtype, 'x-')) {
                $subtype = substr($subtype, 2);
            }
            if (!str_contains($subtype, '+')) {
                return $subtype;
            }
        }

        return null;
    }

    /**
     * Associates a format with mime types.
     *
     * @param string          $format    The format to set
     * @param string|string[] $mimeTypes The associated mime types (the preferred one must be the first as it will be used as the content type)
     */
    public function setFormat(?string $format, string|array $mimeTypes): void
    {
        if (null === $format) {
            trigger_deprecation('symfony/http-foundation', '7.4', 'Passing "null" as the first argument of "%s()" is deprecated. The argument will be non-nullable in Symfony 8.0.', __METHOD__);
            $format = '';
        }

        if (null === static::$formats) {
            static::initializeFormats();
        }

        static::$formats[$format] = (array) $mimeTypes;
    }

    /**
     * Gets the request format.
     *
     * Here is the process to determine the format:
     *
     *  * format defined by the user (with setRequestFormat())
     *  * _format request attribute
     *  * $default
     *
     * @see getPreferredFormat
     */
    public function getRequestFormat(?string $default = 'html'): ?string
    {
        $this->format ??= $this->attributes->get('_format');

        return $this->format ?? $default;
    }

    /**
     * Sets the request format.
     */
    public function setRequestFormat(?string $format): void
    {
        $this->format = $format;
    }

    /**
     * Gets the usual name of the format associated with the request's media type (provided in the Content-Type header).
     *
     * @see Request::$formats
     */
    public function getContentTypeFormat(): ?string
    {
        return $this->getFormat($this->headers->get('CONTENT_TYPE', ''));
    }

    /**
     * Sets the default locale.
     */
    public function setDefaultLocale(string $locale): void
    {
        $this->defaultLocale = $locale;

        if (null === $this->locale) {
            $this->setPhpDefaultLocale($locale);
        }
    }

    /**
     * Get the default locale.
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Sets the locale.
     */
    public function setLocale(string $locale): void
    {
        $this->setPhpDefaultLocale($this->locale = $locale);
    }

    /**
     * Get the locale.
     */
    public function getLocale(): string
    {
        return $this->locale ?? $this->defaultLocale;
    }

    /**
     * Checks if the request method is of specified type.
     *
     * @param string $method Uppercase request method (GET, POST etc)
     */
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * Checks whether or not the method is safe.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.1
     */
    public function isMethodSafe(): bool
    {
        return \in_array($this->getMethod(), ['GET', 'HEAD', 'OPTIONS', 'TRACE', 'QUERY'], true);
    }

    /**
     * Checks whether or not the method is idempotent.
     */
    public function isMethodIdempotent(): bool
    {
        return \in_array($this->getMethod(), ['HEAD', 'GET', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PURGE', 'QUERY'], true);
    }

    /**
     * Checks whether the method is cacheable or not.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.3
     */
    public function isMethodCacheable(): bool
    {
        return \in_array($this->getMethod(), ['GET', 'HEAD', 'QUERY'], true);
    }

    /**
     * Returns the protocol version.
     *
     * If the application is behind a proxy, the protocol version used in the
     * requests between the client and the proxy and between the proxy and the
     * server might be different. This returns the former (from the "Via" header)
     * if the proxy is trusted (see "setTrustedProxies()"), otherwise it returns
     * the latter (from the "SERVER_PROTOCOL" server parameter).
     */
    public function getProtocolVersion(): ?string
    {
        if ($this->isFromTrustedProxy()) {
            preg_match('~^(HTTP/)?([1-9]\.[0-9])\b~', $this->headers->get('Via') ?? '', $matches);

            if ($matches) {
                return 'HTTP/'.$matches[2];
            }
        }

        return $this->server->get('SERVER_PROTOCOL');
    }

    /**
     * Returns the request body content.
     *
     * @param bool $asResource If true, a resource will be returned
     *
     * @return string|resource
     *
     * @psalm-return ($asResource is true ? resource : string)
     */
    public function getContent(bool $asResource = false)
    {
        if ($asResource) {
            if (\is_resource($this->content)) {
                rewind($this->content);

                return $this->content;
            }

            // Content passed in parameter (test)
            if (\is_string($this->content)) {
                $resource = fopen('php://temp', 'r+');
                fwrite($resource, $this->content);
                rewind($resource);

                return $resource;
            }

            $this->content = false;

            return fopen('php://input', 'r');
        }

        if (\is_resource($this->content)) {
            rewind($this->content);

            return stream_get_contents($this->content);
        }

        if (null === $this->content || false === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * Gets the decoded form or json request body.
     *
     * @throws JsonException When the body cannot be decoded to an array
     */
    public function getPayload(): InputBag
    {
        if ($this->request->count()) {
            return clone $this->request;
        }

        if ('' === $content = $this->getContent()) {
            return new InputBag([]);
        }

        try {
            $content = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new JsonException('Could not decode request body.', $e->getCode(), $e);
        }

        if (!\is_array($content)) {
            throw new JsonException(\sprintf('JSON content was expected to decode to an array, "%s" returned.', get_debug_type($content)));
        }

        return new InputBag($content);
    }

    /**
     * Gets the request body decoded as array, typically from a JSON payload.
     *
     * @see getPayload() for portability between content types
     *
     * @throws JsonException When the body cannot be decoded to an array
     */
    public function toArray(): array
    {
        if ('' === $content = $this->getContent()) {
            throw new JsonException('Request body is empty.');
        }

        try {
            $content = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new JsonException('Could not decode request body.', $e->getCode(), $e);
        }

        if (!\is_array($content)) {
            throw new JsonException(\sprintf('JSON content was expected to decode to an array, "%s" returned.', get_debug_type($content)));
        }

        return $content;
    }

    /**
     * Gets the Etags.
     */
    public function getETags(): array
    {
        return preg_split('/\s*,\s*/', $this->headers->get('If-None-Match', ''), -1, \PREG_SPLIT_NO_EMPTY);
    }

    public function isNoCache(): bool
    {
        return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
    }

    /**
     * Gets the preferred format for the response by inspecting, in the following order:
     *   * the request format set using setRequestFormat;
     *   * the values of the Accept HTTP header.
     *
     * Note that if you use this method, you should send the "Vary: Accept" header
     * in the response to prevent any issues with intermediary HTTP caches.
     */
    public function getPreferredFormat(?string $default = 'html'): ?string
    {
        if (!isset($this->preferredFormat) && null !== $preferredFormat = $this->getRequestFormat(null)) {
            $this->preferredFormat = $preferredFormat;
        }

        if ($this->preferredFormat ?? null) {
            return $this->preferredFormat;
        }

        foreach ($this->getAcceptableContentTypes() as $mimeType) {
            if ($this->preferredFormat = $this->getFormat($mimeType)) {
                return $this->preferredFormat;
            }
        }

        return $default;
    }

    /**
     * Returns the preferred language.
     *
     * @param string[] $locales An array of ordered available locales
     */
    public function getPreferredLanguage(?array $locales = null): ?string
    {
        $preferredLanguages = $this->getLanguages();

        if (!$locales) {
            return $preferredLanguages[0] ?? null;
        }

        $locales = array_map($this->formatLocale(...), $locales);
        if (!$preferredLanguages) {
            return $locales[0];
        }

        $combinations = array_merge(...array_map($this->getLanguageCombinations(...), $preferredLanguages));
        foreach ($combinations as $combination) {
            foreach ($locales as $locale) {
                if (str_starts_with($locale, $combination)) {
                    return $locale;
                }
            }
        }

        return $locales[0];
    }

    /**
     * Gets a list of languages acceptable by the client browser ordered in the user browser preferences.
     *
     * @return string[]
     */
    public function getLanguages(): array
    {
        if (null !== $this->languages) {
            return $this->languages;
        }

        $languages = AcceptHeader::fromString($this->headers->get('Accept-Language'))->all();
        $this->languages = [];
        foreach ($languages as $acceptHeaderItem) {
            $lang = $acceptHeaderItem->getValue();
            $this->languages[] = self::formatLocale($lang);
        }
        $this->languages = array_unique($this->languages);

        return $this->languages;
    }

    /**
     * Strips the locale to only keep the canonicalized language value.
     *
     * Depending on the $locale value, this method can return values like :
     * - language_Script_REGION: "fr_Latn_FR", "zh_Hans_TW"
     * - language_Script: "fr_Latn", "zh_Hans"
     * - language_REGION: "fr_FR", "zh_TW"
     * - language: "fr", "zh"
     *
     * Invalid locale values are returned as is.
     *
     * @see https://wikipedia.org/wiki/IETF_language_tag
     * @see https://datatracker.ietf.org/doc/html/rfc5646
     */
    private static function formatLocale(string $locale): string
    {
        [$language, $script, $region] = self::getLanguageComponents($locale);

        return implode('_', array_filter([$language, $script, $region]));
    }

    /**
     * Returns an array of all possible combinations of the language components.
     *
     * For instance, if the locale is "fr_Latn_FR", this method will return:
     * - "fr_Latn_FR"
     * - "fr_Latn"
     * - "fr_FR"
     * - "fr"
     *
     * @return string[]
     */
    private static function getLanguageCombinations(string $locale): array
    {
        [$language, $script, $region] = self::getLanguageComponents($locale);

        return array_unique([
            implode('_', array_filter([$language, $script, $region])),
            implode('_', array_filter([$language, $script])),
            implode('_', array_filter([$language, $region])),
            $language,
        ]);
    }

    /**
     * Returns an array with the language components of the locale.
     *
     * For example:
     * - If the locale is "fr_Latn_FR", this method will return "fr", "Latn", "FR"
     * - If the locale is "fr_FR", this method will return "fr", null, "FR"
     * - If the locale is "zh_Hans", this method will return "zh", "Hans", null
     *
     * @see https://wikipedia.org/wiki/IETF_language_tag
     * @see https://datatracker.ietf.org/doc/html/rfc5646
     *
     * @return array{string, string|null, string|null}
     */
    private static function getLanguageComponents(string $locale): array
    {
        $locale = str_replace('_', '-', strtolower($locale));
        $pattern = '/^([a-zA-Z]{2,3}|i-[a-zA-Z]{5,})(?:-([a-zA-Z]{4}))?(?:-([a-zA-Z]{2}))?(?:-(.+))?$/';
        if (!preg_match($pattern, $locale, $matches)) {
            return [$locale, null, null];
        }
        if (str_starts_with($matches[1], 'i-')) {
            // Language not listed in ISO 639 that are not variants
            // of any listed language, which can be registered with the
            // i-prefix, such as i-cherokee
            $matches[1] = substr($matches[1], 2);
        }

        return [
            $matches[1],
            isset($matches[2]) ? ucfirst(strtolower($matches[2])) : null,
            isset($matches[3]) ? strtoupper($matches[3]) : null,
        ];
    }

    /**
     * Gets a list of charsets acceptable by the client browser in preferable order.
     *
     * @return string[]
     */
    public function getCharsets(): array
    {
        return $this->charsets ??= array_map('strval', array_keys(AcceptHeader::fromString($this->headers->get('Accept-Charset'))->all()));
    }

    /**
     * Gets a list of encodings acceptable by the client browser in preferable order.
     *
     * @return string[]
     */
    public function getEncodings(): array
    {
        return $this->encodings ??= array_map('strval', array_keys(AcceptHeader::fromString($this->headers->get('Accept-Encoding'))->all()));
    }

    /**
     * Gets a list of content types acceptable by the client browser in preferable order.
     *
     * @return string[]
     */
    public function getAcceptableContentTypes(): array
    {
        return $this->acceptableContentTypes ??= array_map('strval', array_keys(AcceptHeader::fromString($this->headers->get('Accept'))->all()));
    }

    /**
     * Returns true if the request is an XMLHttpRequest.
     *
     * It works if your JavaScript library sets an X-Requested-With HTTP header.
     * It is known to work with common JavaScript frameworks:
     *
     * @see https://wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     */
    public function isXmlHttpRequest(): bool
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }

    /**
     * Checks whether the client browser prefers safe content or not according to RFC8674.
     *
     * @see https://tools.ietf.org/html/rfc8674
     */
    public function preferSafeContent(): bool
    {
        if (isset($this->isSafeContentPreferred)) {
            return $this->isSafeContentPreferred;
        }

        if (!$this->isSecure()) {
            // see https://tools.ietf.org/html/rfc8674#section-3
            return $this->isSafeContentPreferred = false;
        }

        return $this->isSafeContentPreferred = AcceptHeader::fromString($this->headers->get('Prefer'))->has('safe');
    }

    /*
     * The following methods are derived from code of the Zend Framework (1.10dev - 2010-01-24)
     *
     * Code subject to the new BSD license (https://framework.zend.com/license).
     *
     * Copyright (c) 2005-2010 Zend Technologies USA Inc. (https://www.zend.com/)
     */

    protected function prepareRequestUri(): string
    {
        $requestUri = '';

        if ($this->isIisRewrite() && '' != $this->server->get('UNENCODED_URL')) {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
            $requestUri = $this->server->get('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');

            if ('' !== $requestUri && '/' === $requestUri[0]) {
                // To only use path and query remove the fragment.
                if (false !== $pos = strpos($requestUri, '#')) {
                    $requestUri = substr($requestUri, 0, $pos);
                }
            } else {
                // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path,
                // only use URL path.
                $uriComponents = parse_url($requestUri);

                if (isset($uriComponents['path'])) {
                    $requestUri = $uriComponents['path'];
                }

                if (isset($uriComponents['query'])) {
                    $requestUri .= '?'.$uriComponents['query'];
                }
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ('' != $this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }

        // normalize the request URI to ease creating sub-requests from this request
        $this->server->set('REQUEST_URI', $requestUri);

        return $requestUri;
    }

    /**
     * Prepares the base URL.
     */
    protected function prepareBaseUrl(): string
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME', ''));

        if (basename($this->server->get('SCRIPT_NAME', '')) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF', '')) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME', '')) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME'); // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $this->server->get('PHP_SELF', '');
            $file = $this->server->get('SCRIPT_FILENAME', '');
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = \count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }

        // Does the baseUrl have anything in common with the request_uri?
        $requestUri = $this->getRequestUri();
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/'.$requestUri;
        }

        if ($baseUrl && null !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return $prefix;
        }

        if ($baseUrl && null !== $prefix = $this->getUrlencodedPrefix($requestUri, rtrim(\dirname($baseUrl), '/'.\DIRECTORY_SEPARATOR).'/')) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/'.\DIRECTORY_SEPARATOR);
        }

        $truncatedRequestUri = $requestUri;
        if (false !== $pos = strpos($requestUri, '?')) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl ?? '');
        if (!$basename || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            // no match whatsoever; set it blank
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if (\strlen($requestUri) >= \strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && 0 !== $pos) {
            $baseUrl = substr($requestUri, 0, $pos + \strlen($baseUrl));
        }

        return rtrim($baseUrl, '/'.\DIRECTORY_SEPARATOR);
    }

    /**
     * Prepares the base path.
     */
    protected function prepareBasePath(): string
    {
        $baseUrl = $this->getBaseUrl();
        if (!$baseUrl) {
            return '';
        }

        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        if (basename($baseUrl) === $filename) {
            $basePath = \dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }

        return rtrim($basePath, '/');
    }

    /**
     * Prepares the path info.
     */
    protected function preparePathInfo(): string
    {
        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }

        // Remove the query string from REQUEST_URI
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/'.$requestUri;
        }

        if (null === ($baseUrl = $this->getBaseUrlReal())) {
            return $requestUri;
        }

        $pathInfo = substr($requestUri, \strlen($baseUrl));
        if ('' === $pathInfo || '/' !== $pathInfo[0]) {
            return '/'.$pathInfo;
        }

        return $pathInfo;
    }

    /**
     * Initializes HTTP request formats.
     */
    protected static function initializeFormats(): void
    {
        static::$formats = [
            'html' => ['text/html', 'application/xhtml+xml'],
            'txt' => ['text/plain'],
            'js' => ['application/javascript', 'application/x-javascript', 'text/javascript'],
            'css' => ['text/css'],
            'json' => ['application/json', 'application/x-json'],
            'jsonld' => ['application/ld+json'],
            'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
            'rdf' => ['application/rdf+xml'],
            'atom' => ['application/atom+xml'],
            'rss' => ['application/rss+xml'],
            'form' => ['application/x-www-form-urlencoded', 'multipart/form-data'],
            'soap' => ['application/soap+xml'],
            'problem' => ['application/problem+json'],
            'hal' => ['application/hal+json', 'application/hal+xml'],
            'jsonapi' => ['application/vnd.api+json'],
            'yaml' => ['text/yaml', 'application/x-yaml'],
            'wbxml' => ['application/vnd.wap.wbxml'],
            'pdf' => ['application/pdf'],
            'csv' => ['text/csv'],
        ];
    }

    private function setPhpDefaultLocale(string $locale): void
    {
        // if either the class Locale doesn't exist, or an exception is thrown when
        // setting the default locale, the intl module is not installed, and
        // the call can be ignored:
        try {
            if (class_exists(\Locale::class, false)) {
                \Locale::setDefault($locale);
            }
        } catch (\Exception) {
        }
    }

    /**
     * Returns the prefix as encoded in the string when the string starts with
     * the given prefix, null otherwise.
     */
    private function getUrlencodedPrefix(string $string, string $prefix): ?string
    {
        if ($this->isIisRewrite()) {
            // ISS with UrlRewriteModule might report SCRIPT_NAME/PHP_SELF with wrong case
            // see https://github.com/php/php-src/issues/11981
            if (0 !== stripos(rawurldecode($string), $prefix)) {
                return null;
            }
        } elseif (!str_starts_with(rawurldecode($string), $prefix)) {
            return null;
        }

        $len = \strlen($prefix);

        if (preg_match(\sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $string, $match)) {
            return $match[0];
        }

        return null;
    }

    private static function createRequestFromFactory(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): static
    {
        if (self::$requestFactory) {
            $request = (self::$requestFactory)($query, $request, $attributes, $cookies, $files, $server, $content);

            if (!$request instanceof self) {
                throw new \LogicException('The Request factory must return an instance of Symfony\Component\HttpFoundation\Request.');
            }

            return $request;
        }

        return new static($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Indicates whether this request originated from a trusted proxy.
     *
     * This can be useful to determine whether or not to trust the
     * contents of a proxy-specific header.
     */
    public function isFromTrustedProxy(): bool
    {
        return self::$trustedProxies && IpUtils::checkIp($this->server->get('REMOTE_ADDR', ''), self::$trustedProxies);
    }

    /**
     * This method is rather heavy because it splits and merges headers, and it's called by many other methods such as
     * getPort(), isSecure(), getHost(), getClientIps(), getBaseUrl() etc. Thus, we try to cache the results for
     * best performance.
     */
    private function getTrustedValues(int $type, ?string $ip = null): array
    {
        $cacheKey = $type."\0".((self::$trustedHeaderSet & $type) ? $this->headers->get(self::TRUSTED_HEADERS[$type]) : '');
        $cacheKey .= "\0".$ip."\0".$this->headers->get(self::TRUSTED_HEADERS[self::HEADER_FORWARDED]);

        if (isset($this->trustedValuesCache[$cacheKey])) {
            return $this->trustedValuesCache[$cacheKey];
        }

        $clientValues = [];
        $forwardedValues = [];

        if ((self::$trustedHeaderSet & $type) && $this->headers->has(self::TRUSTED_HEADERS[$type])) {
            foreach (explode(',', $this->headers->get(self::TRUSTED_HEADERS[$type])) as $v) {
                $clientValues[] = (self::HEADER_X_FORWARDED_PORT === $type ? '0.0.0.0:' : '').trim($v);
            }
        }

        if ((self::$trustedHeaderSet & self::HEADER_FORWARDED) && (isset(self::FORWARDED_PARAMS[$type])) && $this->headers->has(self::TRUSTED_HEADERS[self::HEADER_FORWARDED])) {
            $forwarded = $this->headers->get(self::TRUSTED_HEADERS[self::HEADER_FORWARDED]);
            $parts = HeaderUtils::split($forwarded, ',;=');
            $param = self::FORWARDED_PARAMS[$type];
            foreach ($parts as $subParts) {
                if (null === $v = HeaderUtils::combine($subParts)[$param] ?? null) {
                    continue;
                }
                if (self::HEADER_X_FORWARDED_PORT === $type) {
                    if (str_ends_with($v, ']') || false === $v = strrchr($v, ':')) {
                        $v = $this->isSecure() ? ':443' : ':80';
                    }
                    $v = '0.0.0.0'.$v;
                }
                $forwardedValues[] = $v;
            }
        }

        if (null !== $ip) {
            $clientValues = $this->normalizeAndFilterClientIps($clientValues, $ip);
            $forwardedValues = $this->normalizeAndFilterClientIps($forwardedValues, $ip);
        }

        if ($forwardedValues === $clientValues || !$clientValues) {
            return $this->trustedValuesCache[$cacheKey] = $forwardedValues;
        }

        if (!$forwardedValues) {
            return $this->trustedValuesCache[$cacheKey] = $clientValues;
        }

        if (!$this->isForwardedValid) {
            return $this->trustedValuesCache[$cacheKey] = null !== $ip ? ['0.0.0.0', $ip] : [];
        }
        $this->isForwardedValid = false;

        throw new ConflictingHeadersException(\sprintf('The request has both a trusted "%s" header and a trusted "%s" header, conflicting with each other. You should either configure your proxy to remove one of them, or configure your project to distrust the offending one.', self::TRUSTED_HEADERS[self::HEADER_FORWARDED], self::TRUSTED_HEADERS[$type]));
    }

    private function normalizeAndFilterClientIps(array $clientIps, string $ip): array
    {
        if (!$clientIps) {
            return [];
        }
        $clientIps[] = $ip; // Complete the IP chain with the IP the request actually came from
        $firstTrustedIp = null;

        foreach ($clientIps as $key => $clientIp) {
            if (strpos($clientIp, '.')) {
                // Strip :port from IPv4 addresses. This is allowed in Forwarded
                // and may occur in X-Forwarded-For.
                $i = strpos($clientIp, ':');
                if ($i) {
                    $clientIps[$key] = $clientIp = substr($clientIp, 0, $i);
                }
            } elseif (str_starts_with($clientIp, '[')) {
                // Strip brackets and :port from IPv6 addresses.
                $i = strpos($clientIp, ']', 1);
                $clientIps[$key] = $clientIp = substr($clientIp, 1, $i - 1);
            }

            if (!filter_var($clientIp, \FILTER_VALIDATE_IP)) {
                unset($clientIps[$key]);

                continue;
            }

            if (IpUtils::checkIp($clientIp, self::$trustedProxies)) {
                unset($clientIps[$key]);

                // Fallback to this when the client IP falls into the range of trusted proxies
                $firstTrustedIp ??= $clientIp;
            }
        }

        // Now the IP chain contains only untrusted proxies and the client IP
        return $clientIps ? array_reverse($clientIps) : [$firstTrustedIp];
    }

    /**
     * Is this IIS with UrlRewriteModule?
     *
     * This method consumes, caches and removed the IIS_WasUrlRewritten env var,
     * so we don't inherit it to sub-requests.
     */
    private function isIisRewrite(): bool
    {
        if (1 === $this->server->getInt('IIS_WasUrlRewritten')) {
            $this->isIisRewrite = true;
            $this->server->remove('IIS_WasUrlRewritten');
        }

        return $this->isIisRewrite;
    }

    /**
     * See https://url.spec.whatwg.org/.
     */
    private static function isHostValid(string $host): bool
    {
        if ('[' === $host[0]) {
            return ']' === $host[-1] && filter_var(substr($host, 1, -1), \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6);
        }

        if (preg_match('/\.[0-9]++\.?$/D', $host)) {
            return null !== filter_var($host, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4 | \FILTER_NULL_ON_FAILURE);
        }

        // use preg_replace() instead of preg_match() to prevent DoS attacks with long host names
        return '' === preg_replace('/[-a-zA-Z0-9_]++\.?/', '', $host);
    }
}
