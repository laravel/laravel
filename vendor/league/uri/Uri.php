<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri;

use BackedEnum;
use Closure;
use Deprecated;
use finfo;
use League\Uri\Contracts\Conditionable;
use League\Uri\Contracts\FragmentDirective;
use League\Uri\Contracts\Transformable;
use League\Uri\Contracts\UriComponentInterface;
use League\Uri\Contracts\UriException;
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\MissingFeature;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\Idna\Converter as IdnaConverter;
use League\Uri\IPv4\Converter as IPv4Converter;
use League\Uri\IPv6\Converter as IPv6Converter;
use League\Uri\UriTemplate\TemplateCanNotBeExpanded;
use Psr\Http\Message\UriInterface as Psr7UriInterface;
use RuntimeException;
use SensitiveParameter;
use SplFileInfo;
use SplFileObject;
use Stringable;
use Throwable;
use TypeError;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;

use function array_filter;
use function array_key_last;
use function array_map;
use function array_pop;
use function array_shift;
use function base64_decode;
use function base64_encode;
use function basename;
use function count;
use function dirname;
use function explode;
use function fclose;
use function feof;
use function file_get_contents;
use function filter_var;
use function fopen;
use function fread;
use function fwrite;
use function gettype;
use function implode;
use function in_array;
use function is_bool;
use function is_object;
use function is_resource;
use function is_string;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function rawurldecode;
use function rawurlencode;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function str_contains;
use function str_repeat;
use function str_replace;
use function str_starts_with;
use function strlen;
use function strpos;
use function strspn;
use function strtolower;
use function substr;
use function trim;

use const FILEINFO_MIME;
use const FILEINFO_MIME_TYPE;
use const FILTER_FLAG_IPV4;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;
use const FILTER_VALIDATE_EMAIL;
use const FILTER_VALIDATE_IP;

/**
 * @phpstan-import-type ComponentMap from UriString
 * @phpstan-import-type InputComponentMap from UriString
 */
final class Uri implements Conditionable, UriInterface, Transformable
{
    /**
     * RFC3986 invalid characters.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-2.2
     *
     * @var string
     */
    private const REGEXP_INVALID_CHARS = '/[\x00-\x1f\x7f]/';

    /**
     * RFC3986 IPvFuture host and port component.
     *
     * @var string
     */
    private const REGEXP_HOST_PORT = ',^(?<host>(\[.*]|[^:])*)(:(?<port>[^/?#]*))?$,x';

    /**
     * Regular expression pattern to for file URI.
     * <volume> contains the volume but not the volume separator.
     * The volume separator may be URL-encoded (`|` as `%7C`) by formatPath(),
     * so we account for that here.
     *
     * @var string
     */
    private const REGEXP_FILE_PATH = ',^(?<delim>/)?(?<volume>[a-zA-Z])(?:[:|\|]|%7C)(?<rest>.*)?,';

    /**
     * Mimetype regular expression pattern.
     *
     * @link https://tools.ietf.org/html/rfc2397
     *
     * @var string
     */
    private const REGEXP_MIMETYPE = ',^\w+/[-.\w]+(?:\+[-.\w]+)?$,';

    /**
     * Base64 content regular expression pattern.
     *
     * @link https://tools.ietf.org/html/rfc2397
     *
     * @var string
     */
    private const REGEXP_BINARY = ',(;|^)base64$,';

    /**
     * Windows filepath regular expression pattern.
     * <root> contains both the volume and volume separator.
     *
     * @var string
     */
    private const REGEXP_WINDOW_PATH = ',^(?<root>[a-zA-Z][:|\|]),';

    /**
     * Maximum number of cached items.
     *
     * @var int
     */
    private const MAXIMUM_CACHED_ITEMS = 100;

    /**
     * All ASCII letters sorted by typical frequency of occurrence.
     *
     * @var string
     */
    private const ASCII = "\x20\x65\x69\x61\x73\x6E\x74\x72\x6F\x6C\x75\x64\x5D\x5B\x63\x6D\x70\x27\x0A\x67\x7C\x68\x76\x2E\x66\x62\x2C\x3A\x3D\x2D\x71\x31\x30\x43\x32\x2A\x79\x78\x29\x28\x4C\x39\x41\x53\x2F\x50\x22\x45\x6A\x4D\x49\x6B\x33\x3E\x35\x54\x3C\x44\x34\x7D\x42\x7B\x38\x46\x77\x52\x36\x37\x55\x47\x4E\x3B\x4A\x7A\x56\x23\x48\x4F\x57\x5F\x26\x21\x4B\x3F\x58\x51\x25\x59\x5C\x09\x5A\x2B\x7E\x5E\x24\x40\x60\x7F\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F";

    private readonly ?string $scheme;
    private readonly ?string $user;
    private readonly ?string $pass;
    private readonly ?string $userInfo;
    private readonly ?string $host;
    private readonly ?int $port;
    private readonly ?string $authority;
    private readonly string $path;
    private readonly ?string $query;
    private readonly ?string $fragment;
    private readonly string $uriAsciiString;
    private readonly string $uriUnicodeString;
    private readonly ?string $origin;

    private function __construct(
        ?string $scheme,
        ?string $user,
        #[SensitiveParameter] ?string $pass,
        ?string $host,
        ?int $port,
        string $path,
        ?string $query,
        ?string $fragment
    ) {
        $this->scheme = $this->formatScheme($scheme);
        $this->user = Encoder::encodeUser($user);
        $this->pass = Encoder::encodePassword($pass);
        $this->host = $this->formatHost($host);
        $this->port = $this->formatPort($port);
        $this->authority = UriString::buildAuthority([
            'scheme' => $this->scheme,
            'user' => $this->user,
            'pass' => $this->pass,
            'host' => $this->host,
            'port' => $this->port,
        ]);
        $this->path = $this->formatPath($path);
        $this->query = Encoder::encodeQueryOrFragment($query);
        $this->fragment = Encoder::encodeQueryOrFragment($fragment);
        $this->userInfo = null !== $this->pass ? $this->user.':'.$this->pass : $this->user;
        $this->uriAsciiString = UriString::buildUri($this->scheme, $this->authority, $this->path, $this->query, $this->fragment);
        $this->assertValidRfc3986Uri();
        $this->assertValidState();
        $this->origin = $this->setOrigin();
        $host = $this->getUnicodeHost();
        $this->uriUnicodeString = $host === $this->host
            ? $this->uriAsciiString
            : UriString::buildUri(
                $this->scheme,
                UriString::buildAuthority([...$this->toComponents(), ...['host' => $host]]),
                $this->path,
                $this->query,
                $this->fragment
            );
    }

    /**
     * Format the Scheme and Host component.
     *
     * @throws SyntaxError if the scheme is invalid
     */
    private function formatScheme(?string $scheme): ?string
    {
        if (null === $scheme) {
            return null;
        }

        $formattedScheme = strtolower($scheme);
        static $cache = [];
        if (isset($cache[$formattedScheme])) {
            return $formattedScheme;
        }

        null !== UriScheme::tryFrom($formattedScheme)
        || UriString::isValidScheme($formattedScheme)
        || throw new SyntaxError('The scheme `'.$scheme.'` is invalid.');


        $cache[$formattedScheme] = 1;
        if (self::MAXIMUM_CACHED_ITEMS < count($cache)) {
            array_shift($cache);
        }

        return $formattedScheme;
    }

    /**
     * Validate and Format the Host component.
     */
    private function formatHost(?string $host): ?string
    {
        return HostRecord::from($host)->toAscii();
    }

    /**
     * Format the Port component.
     *
     * @throws SyntaxError
     */
    private function formatPort(BackedEnum|int|null $port = null): ?int
    {
        if ($port instanceof BackedEnum) {
            $port = (string) $port->value;
            1 === preg_match('/^\d+$/', $port) || throw new SyntaxError('The port `'.$port.'` is invalid.');
            $port = (int) $port;
        }

        $defaultPort = null !== $this->scheme
            ? UriScheme::tryFrom($this->scheme)?->port()
            : null;

        return match (true) {
            null === $port, $defaultPort === $port => null,
            0 > $port => throw new SyntaxError('The port `'.$port.'` is invalid.'),
            default => $port,
        };
    }

    /**
     * Create a new instance from a string or a stringable structure or returns null on failure.
     */
    public static function tryNew(Rfc3986Uri|WhatWgUrl|Urn|Stringable|string $uri = ''): ?self
    {
        try {
            return self::new($uri);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Create a new instance from a string.
     */
    public static function new(Rfc3986Uri|WhatWgUrl|Urn|BackedEnum|Stringable|string $uri = ''): self
    {
        if ($uri instanceof Rfc3986Uri) {
            return new self(
                $uri->getRawScheme(),
                $uri->getRawUsername(),
                $uri->getRawPassword(),
                $uri->getRawHost(),
                $uri->getPort(),
                $uri->getRawPath(),
                $uri->getRawQuery(),
                $uri->getRawFragment()
            );
        }

        if ($uri instanceof WhatWgUrl) {
            return new self(
                $uri->getScheme(),
                $uri->getUsername(),
                $uri->getPassword(),
                $uri->getAsciiHost(),
                $uri->getPort(),
                $uri->getPath(),
                $uri->getQuery(),
                $uri->getFragment(),
            );
        }

        if ($uri instanceof BackedEnum) {
            $uri = $uri->value;
        }

        $uri = (string) $uri;
        trim($uri) === $uri || throw new SyntaxError(sprintf('The uri `%s` contains invalid characters', $uri));

        return new self(...UriString::parse(str_replace(' ', '%20', $uri)));
    }

    /**
     * Returns a new instance from a URI and a Base URI.or null on failure.
     *
     * The returned URI must be absolute if a base URI is provided
     */
    public static function parse(Rfc3986Uri|WhatWgUrl|Urn|BackedEnum|Stringable|string $uri, Rfc3986Uri|WhatWgUrl|Urn|BackedEnum|Stringable|string|null $baseUri = null): ?self
    {
        try {
            if (null === $baseUri) {
                return self::new($uri);
            }

            if ($uri instanceof Rfc3986Uri) {
                $uri = $uri->toRawString();
            }

            if ($uri instanceof WhatWgUrl) {
                $uri = $uri->toAsciiString();
            }

            if ($baseUri instanceof Rfc3986Uri) {
                $baseUri = $baseUri->toRawString();
            }

            if ($baseUri instanceof WhatWgUrl) {
                $baseUri = $baseUri->toAsciiString();
            }

            return self::new(UriString::resolve($uri, $baseUri));
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Creates a new instance from a template.
     *
     * @throws TemplateCanNotBeExpanded if the variables are invalid or missing
     * @throws UriException if the resulting expansion cannot be converted to a UriInterface instance
     */
    public static function fromTemplate(BackedEnum|UriTemplate|Stringable|string $template, iterable $variables = []): self
    {
        return match (true) {
            $template instanceof UriTemplate => self::new($template->expand($variables)),
            $template instanceof UriTemplate\Template => self::new($template->expand($variables)),
            default => self::new(UriTemplate\Template::new($template)->expand($variables)),
        };
    }

    /**
     * Create a new instance from a hash representation of the URI similar
     * to PHP parse_url function result.
     *
     * @param InputComponentMap $components a hash representation of the URI similar to PHP parse_url function result
     */
    public static function fromComponents(array $components = []): self
    {
        $components += [
            'scheme' => null, 'user' => null, 'pass' => null, 'host' => null,
            'port' => null, 'path' => '', 'query' => null, 'fragment' => null,
        ];

        if (null === $components['path']) {
            $components['path'] = '';
        }

        return new self(
            $components['scheme'],
            $components['user'],
            $components['pass'],
            $components['host'],
            $components['port'],
            $components['path'],
            $components['query'],
            $components['fragment']
        );
    }

    /**
     * Create a new instance from a data file path.
     *
     * @param SplFileInfo|SplFileObject|resource|Stringable|string $path
     * @param ?resource $context
     *
     * @throws MissingFeature If ext/fileinfo is not installed
     * @throws SyntaxError If the file does not exist or is not readable
     */
    public static function fromFileContents(mixed $path, $context = null): self
    {
        FeatureDetection::supportsFileDetection();
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $bufferSize = 8192;

        /** @var Closure(SplFileobject): array{0:string, 1:string} $fromFileObject */
        $fromFileObject = function (SplFileObject $path) use ($finfo, $bufferSize): array {
            $raw = $path->fread($bufferSize);
            false !== $raw || throw new SyntaxError('The file `'.$path.'` does not exist or is not readable.');

            $mimetype = (string) $finfo->buffer($raw);
            while (!$path->eof()) {
                $raw .= $path->fread($bufferSize);
            }

            return [$mimetype, $raw];
        };

        /** @var Closure(resource): array{0:string, 1:string} $fromResource */
        $fromResource = function ($stream) use ($finfo, $path, $bufferSize): array {
            set_error_handler(fn (int $errno, string $errstr, string $errfile, int $errline) => true);
            $raw = fread($stream, $bufferSize);
            false !== $raw || throw new SyntaxError('The file `'.$path.'` does not exist or is not readable.');

            $mimetype = (string) $finfo->buffer($raw);
            while (!feof($stream)) {
                $raw .= fread($stream, $bufferSize);
            }
            restore_error_handler();

            return [$mimetype, $raw];
        };

        /** @var Closure(Stringable|string, resource|null): array{0:string, 1:string} $fromPath */
        $fromPath = function (Stringable|string $path, $context) use ($finfo): array {
            $path = (string) $path;
            set_error_handler(fn (int $errno, string $errstr, string $errfile, int $errline) => true);
            $raw = file_get_contents(filename: $path, context: $context);
            restore_error_handler();
            false !== $raw || throw new SyntaxError('The file `'.$path.'` does not exist or is not readable.');
            $mimetype = (string) $finfo->file(filename: $path, flags: FILEINFO_MIME, context: $context);

            return [$mimetype, $raw];
        };

        [$mimetype, $raw] = match (true) {
            $path instanceof SplFileObject => $fromFileObject($path),
            $path instanceof SplFileInfo => $fromFileObject($path->openFile(mode: 'rb', context: $context)),
            is_resource($path) => $fromResource($path),
            $path instanceof Stringable,
            is_string($path) => $fromPath($path, $context),
            default => throw new TypeError('The path `'.$path.'` is not a valid resource.'),
        };

        return Uri::fromComponents([
            'scheme' => 'data',
            'path' => str_replace(' ', '', $mimetype.';base64,'.base64_encode($raw)),
        ]);
    }

    /**
     * Create a new instance from a data URI string.
     *
     * @throws SyntaxError If the parameter syntax is invalid
     */
    public static function fromData(BackedEnum|Stringable|string $data, string $mimetype = '', string $parameters = ''): self
    {
        static $regexpMimetype = ',^\w+/[-.\w]+(?:\+[-.\w]+)?$,';

        $mimetype = match (true) {
            '' === $mimetype => 'text/plain',
            1 === preg_match($regexpMimetype, $mimetype) =>  $mimetype,
            default => throw new SyntaxError('Invalid mimeType, `'.$mimetype.'`.'),
        };

        if ($data instanceof BackedEnum) {
            $data = $data->value;
        }

        $data = (string) $data;
        if ('' === $parameters) {
            return self::fromComponents([
                'scheme' => 'data',
                'path' => self::formatDataPath($mimetype.','.rawurlencode($data)),
            ]);
        }

        $isInvalidParameter = static function (string $parameter): bool {
            $properties = explode('=', $parameter);

            return 2 !== count($properties) || 'base64' === strtolower($properties[0]);
        };

        if (str_starts_with($parameters, ';')) {
            $parameters = substr($parameters, 1);
        }

        return match ([]) {
            array_filter(explode(';', $parameters), $isInvalidParameter) => self::fromComponents([
                'scheme' => 'data',
                'path' => self::formatDataPath($mimetype.';'.$parameters.','.rawurlencode($data)),
            ]),
            default => throw new SyntaxError(sprintf('Invalid mediatype parameters, `%s`.', $parameters))
        };
    }

    /**
     * Create a new instance from a Unix path string.
     */
    public static function fromUnixPath(BackedEnum|Stringable|string $path): self
    {
        if ($path instanceof BackedEnum) {
            $path = $path->value;
        }

        $path = implode('/', array_map(rawurlencode(...), explode('/', (string) $path)));

        return Uri::fromComponents(match (true) {
            '/' !== ($path[0] ?? '') => ['path' => $path],
            default => ['path' => $path, 'scheme' => 'file', 'host' => ''],
        });
    }

    /**
     * Create a new instance from a local Windows path string.
     */
    public static function fromWindowsPath(BackedEnum|Stringable|string $path): self
    {
        if ($path instanceof BackedEnum) {
            $path = $path->value;
        }

        $root = '';
        $path = (string) $path;
        if (1 === preg_match(self::REGEXP_WINDOW_PATH, $path, $matches)) {
            $root = substr($matches['root'], 0, -1).':';
            $path = substr($path, strlen($root));
        }
        $path = str_replace('\\', '/', $path);
        $path = implode('/', array_map(rawurlencode(...), explode('/', $path)));

        //Local Windows absolute path
        if ('' !== $root) {
            return Uri::fromComponents(['path' => '/'.$root.$path, 'scheme' => 'file', 'host' => '']);
        }

        //UNC Windows Path
        if (!str_starts_with($path, '//')) {
            return Uri::fromComponents(['path' => $path]);
        }

        [$host, $path] = explode('/', substr($path, 2), 2) + [1 => ''];

        return Uri::fromComponents(['host' => $host, 'path' => '/'.$path, 'scheme' => 'file']);
    }

    /**
     * Creates a new instance from a RFC8089 compatible URI.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc8089
     */
    public static function fromRfc8089(BackedEnum|Stringable|string $uri): static
    {
        if ($uri instanceof BackedEnum) {
            $uri = $uri->value;
        }

        $fileUri = self::new((string) preg_replace(',^(file:/)([^/].*)$,i', 'file:///$2', (string) $uri));
        $scheme = $fileUri->getScheme();

        return match (true) {
            'file' !== $scheme => throw new SyntaxError('As per RFC8089, the URI scheme must be `file`.'),
            'localhost' === $fileUri->getAuthority() => $fileUri->withHost(''),
            default => $fileUri,
        };
    }

    /**
     * Create a new instance from the environment.
     */
    public static function fromServer(array $server): self
    {
        $components = ['scheme' => self::fetchScheme($server)];
        [$components['user'], $components['pass']] = self::fetchUserInfo($server);
        [$components['host'], $components['port']] = self::fetchHostname($server);
        [$components['path'], $components['query']] = self::fetchRequestUri($server);

        return Uri::fromComponents($components);
    }

    /**
     * Returns the environment scheme.
     */
    private static function fetchScheme(array $server): string
    {
        $server += ['HTTPS' => ''];

        return match (true) {
            false !== filter_var($server['HTTPS'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) => 'https',
            default => 'http',
        };
    }

    /**
     * Returns the environment user info.
     *
     * @return non-empty-array {0: ?string, 1: ?string}
     */
    private static function fetchUserInfo(array $server): array
    {
        $server += ['PHP_AUTH_USER' => null, 'PHP_AUTH_PW' => null, 'HTTP_AUTHORIZATION' => ''];
        $user = $server['PHP_AUTH_USER'];
        $pass = $server['PHP_AUTH_PW'];
        if (str_starts_with(strtolower($server['HTTP_AUTHORIZATION']), 'basic')) {
            $userinfo = base64_decode(substr($server['HTTP_AUTHORIZATION'], 6), true);
            false !== $userinfo || throw new SyntaxError('The user info could not be detected');
            [$user, $pass] = explode(':', $userinfo, 2) + [1 => null];
        }

        if (null !== $user) {
            $user = rawurlencode($user);
        }

        if (null !== $pass) {
            $pass = rawurlencode($pass);
        }

        return [$user, $pass];
    }

    /**
     * Returns the environment host.
     *
     * @throws SyntaxError If the host cannot be detected
     *
     * @return array{0:string|null, 1:int|null}
     */
    private static function fetchHostname(array $server): array
    {
        $server += ['SERVER_PORT' => null];
        if (null !== $server['SERVER_PORT']) {
            $server['SERVER_PORT'] = (int) $server['SERVER_PORT'];
        }

        if (isset($server['HTTP_HOST']) && 1 === preg_match(self::REGEXP_HOST_PORT, $server['HTTP_HOST'], $matches)) {
            $matches += ['host' => null, 'port' => null];
            if (null !== $matches['port']) {
                $matches['port'] = (int) $matches['port'];
            }

            return [$matches['host'], $matches['port'] ?? $server['SERVER_PORT']];
        }

        isset($server['SERVER_ADDR']) || throw new SyntaxError('The host could not be detected');
        if (false === filter_var($server['SERVER_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return ['['.$server['SERVER_ADDR'].']', $server['SERVER_PORT']];
        }

        return [$server['SERVER_ADDR'], $server['SERVER_PORT']];
    }

    /**
     * Returns the environment path.
     *
     * @return list<?string>
     */
    private static function fetchRequestUri(array $server): array
    {
        $server += ['IIS_WasUrlRewritten' => null, 'UNENCODED_URL' => '', 'PHP_SELF' => '', 'QUERY_STRING' => null];
        if ('1' === $server['IIS_WasUrlRewritten'] && '' !== $server['UNENCODED_URL']) {
            return explode('?', $server['UNENCODED_URL'], 2) + [1 => null];
        }

        if (isset($server['REQUEST_URI'])) {
            [$path] = explode('?', $server['REQUEST_URI'], 2);
            $query = ('' !== $server['QUERY_STRING']) ? $server['QUERY_STRING'] : null;

            return [$path, $query];
        }

        return [$server['PHP_SELF'], $server['QUERY_STRING']];
    }

    /**
     * Format the Path component.
     */
    private function formatPath(string $path): string
    {
        $path = match ($this->scheme) {
            'data' => Encoder::encodePath(self::formatDataPath($path)),
            'file' => self::formatFilePath(Encoder::encodePath($path)),
            default => Encoder::encodePath($path),
        };

        if ('' === $path) {
            return $path;
        }

        if (null !== $this->authority) {
            // If there is an authority, the path must start with a `/`
            return str_starts_with($path, '/') ? $path : '/'.$path;
        }

        // If there is no authority, the path cannot start with `//`
        if (str_starts_with($path, '//')) {
            return '/.'.$path;
        }

        $colonPos = strpos($path, ':');
        if (false !== $colonPos && null === $this->scheme) {
            // In the absence of a scheme and of an authority,
            // the first path segment cannot contain a colon (":") character.'
            $slashPos = strpos($path, '/');
            (false !== $slashPos && $colonPos > $slashPos) || throw new SyntaxError(
                'In absence of the scheme and authority components, the first path segment cannot contain a colon (":") character.'
            );
        }

        return $path;
    }

    /**
     * Filter the Path component.
     *
     * @link https://tools.ietf.org/html/rfc2397
     *
     * @throws SyntaxError If the path is not compliant with RFC2397
     */
    private static function formatDataPath(string $path): string
    {
        if ('' == $path) {
            return 'text/plain;charset=us-ascii,';
        }

        if (strlen($path) !== strspn($path, self::ASCII) || !str_contains($path, ',')) {
            throw new SyntaxError('The path `'.$path.'` is invalid according to RFC2937.');
        }

        $parts = explode(',', $path, 2) + [1 => null];
        $mediatype = explode(';', (string) $parts[0], 2) + [1 => null];
        $data = (string) $parts[1];
        $mimetype = $mediatype[0];
        if (null === $mimetype || '' === $mimetype) {
            $mimetype = 'text/plain';
        }

        $parameters = $mediatype[1];
        if (null === $parameters || '' === $parameters) {
            $parameters = 'charset=us-ascii';
        }

        self::assertValidPath($mimetype, $parameters, $data);

        return $mimetype.';'.$parameters.','.$data;
    }

    /**
     * Assert the path is a compliant with RFC2397.
     *
     * @link https://tools.ietf.org/html/rfc2397
     *
     * @throws SyntaxError If the mediatype or the data are not compliant with the RFC2397
     */
    private static function assertValidPath(string $mimetype, string $parameters, string $data): void
    {
        1 === preg_match(self::REGEXP_MIMETYPE, $mimetype) || throw new SyntaxError('The path mimetype `'.$mimetype.'` is invalid.');
        $isBinary = 1 === preg_match(self::REGEXP_BINARY, $parameters, $matches);
        if ($isBinary) {
            $parameters = substr($parameters, 0, - strlen($matches[0]));
        }

        $res = array_filter(array_filter(explode(';', $parameters), self::validateParameter(...)));
        [] === $res || throw new SyntaxError('The path parameters `'.$parameters.'` is invalid.');
        if (!$isBinary) {
            return;
        }

        $res = base64_decode($data, true);
        if (false === $res || $data !== base64_encode($res)) {
            throw new SyntaxError('The path data `'.$data.'` is invalid.');
        }
    }

    /**
     * Validate mediatype parameter.
     */
    private static function validateParameter(string $parameter): bool
    {
        $properties = explode('=', $parameter);

        return 2 != count($properties) || 'base64' === strtolower($properties[0]);
    }

    /**
     * Format the path component for the URI scheme file.
     */
    private static function formatFilePath(string $path): string
    {
        return (string) preg_replace_callback(
            self::REGEXP_FILE_PATH,
            static fn (array $matches): string => $matches['delim'].$matches['volume'].(isset($matches['rest']) ? ':'.$matches['rest'] : ''),
            $path
        );
    }

    /**
     * assert the URI internal state is valid.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3
     * @link https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @throws SyntaxError if the URI is in an invalid state, according to RFC3986
     */
    private function assertValidRfc3986Uri(): void
    {
        if (null !== $this->authority && ('' !== $this->path && '/' !== $this->path[0])) {
            throw new SyntaxError('If an authority is present the path must be empty or start with a `/`.');
        }

        if (null === $this->authority && str_starts_with($this->path, '//')) {
            throw new SyntaxError('If there is no authority the path `'.$this->path.'` cannot start with a `//`.');
        }

        $pos = strpos($this->path, ':');
        if (null === $this->authority
            && null === $this->scheme
            && false !== $pos
            && !str_contains(substr($this->path, 0, $pos), '/')
        ) {
            throw new SyntaxError('In absence of a scheme and an authority the first path segment cannot contain a colon (":") character.');
        }
    }

    /**
     * assert the URI scheme is valid.
     *
     * @link https://w3c.github.io/FileAPI/#url
     * @link https://datatracker.ietf.org/doc/html/rfc2397
     * @link https://tools.ietf.org/html/rfc3986#section-3
     * @link https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @throws SyntaxError if the URI is in an invalid state, according to scheme-specific rules
     */
    private function assertValidState(): void
    {
        $scheme = UriScheme::tryFrom((string) $this->scheme);
        if (null === $scheme) {
            return;
        }

        $schemeType = $scheme->type();
        match ($scheme) {
            UriScheme::Blob => $this->isValidBlob(),
            UriScheme::Mailto => $this->isValidMailto(),
            UriScheme::Data,
            UriScheme::About,
            UriScheme::Javascript => $this->isUriWithSchemeAndPathOnly(),
            UriScheme::File => $this->isUriWithSchemeHostAndPathOnly(),
            UriScheme::Ftp,
            UriScheme::Gopher,
            UriScheme::Afp,
            UriScheme::Dict,
            UriScheme::Msrps,
            UriScheme::Msrp,
            UriScheme::Mtqp,
            UriScheme::Rsync,
            UriScheme::Ssh,
            UriScheme::Svn,
            UriScheme::Snmp => $this->isNonEmptyHostUriWithoutFragmentAndQuery(),
            UriScheme::Https,
            UriScheme::Http => $this->isNonEmptyHostUri(),
            UriScheme::Ws,
            UriScheme::Wss,
            UriScheme::Ipp,
            UriScheme::Ipps => $this->isNonEmptyHostUriWithoutFragment(),
            UriScheme::Ldap,
            UriScheme::Ldaps,
            UriScheme::Acap,
            UriScheme::Imaps,
            UriScheme::Imap,
            UriScheme::Redis => null === $this->fragment,
            UriScheme::Prospero => null === $this->fragment && null === $this->query && null === $this->userInfo,
            UriScheme::Urn => null !== Urn::parse($this->uriAsciiString),
            UriScheme::Telnet,
            UriScheme::Tn3270 => null === $this->fragment && null === $this->query && in_array($this->path, ['', '/'], true),
            UriScheme::Vnc => null !==  $this->authority && null === $this->fragment && '' === $this->path,
            default => $schemeType->isUnknown()
                || ($schemeType->isOpaque() && null === $this->authority)
                || ($schemeType->isHierarchical() && null !== $this->authority),
        } || throw new SyntaxError('The uri `'.$this->uriAsciiString.'` is invalid for the `'.$this->scheme.'` scheme.');
    }

    private function isValidBlob(): bool
    {
        static $regexpUuidRfc4122 = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

        if (!$this->isUriWithSchemeAndPathOnly()
            || '' === $this->path
            || !str_contains($this->path, '/')
            || str_ends_with($this->path, '/')
            || 1 !== preg_match($regexpUuidRfc4122, basename($this->path))
        ) {
            return false;
        }

        $origin = dirname($this->path);
        if ('null' === $origin) {
            return true;
        }

        try {
            $components = UriString::parse($origin);

            return '' === $components['path']
                && null === $components['query']
                && null === $components['fragment']
                && true === UriScheme::tryFrom((string) $components['scheme'])?->isWhatWgSpecial();
        } catch (UriException) {
            return false;
        }
    }

    private function isValidMailto(): bool
    {
        if (null !== $this->authority || null !== $this->fragment || str_contains((string) $this->query, '?')) {
            return false;
        }

        static $mailHeaders = [
            'to', 'cc', 'bcc', 'reply-to', 'from', 'sender',
            'resent-to', 'resent-cc', 'resent-bcc', 'resent-from', 'resent-sender',
            'return-path', 'delivery-to', 'site-owner',
        ];

        static $headerRegexp = '/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/D';
        $pairs = QueryString::parseFromValue($this->query);
        $hasTo = false;
        foreach ($pairs as [$name, $value]) {
            $headerName = strtolower($name);
            if (in_array($headerName, $mailHeaders, true)) {
                if (null === $value || !self::validateEmailList($value)) {
                    return false;
                }

                if (!$hasTo && 'to' === $headerName) {
                    $hasTo = true;
                }
                continue;
            }

            if (1 !== preg_match($headerRegexp, (string) Encoder::decodeAll($name))) {
                return false;
            }
        }

        return '' === $this->path ? $hasTo : self::validateEmailList($this->path);
    }

    private static function validateEmailList(string $emails): bool
    {
        foreach (explode(',', $emails) as $email) {
            if (false === filter_var((string) Encoder::decodeAll($email), FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        return '' !== $emails;
    }

    /**
     * Sets the URI origin.
     *
     * The origin read-only property of the URL interface returns a string containing
     * the Unicode serialization of the represented URL.
     */
    private function setOrigin(): ?string
    {
        try {
            if ('blob' !== $this->scheme) {
                if (!(UriScheme::tryFrom($this->scheme ?? '')?->isWhatWgSpecial() ?? false)) {
                    return null;
                }

                $host = $this->host;
                $converted = $host;
                if (null !== $converted) {
                    try {
                        $converted = IPv4Converter::fromEnvironment()->toDecimal($host);
                    } catch (MissingFeature) {
                        $converted = null;
                    }

                    if (false === filter_var($converted, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $converted = IPv6Converter::compress($host);
                    }

                    /** @var string $converted */
                    if ($converted !== $host) {
                        $converted = Idna\Converter::toAscii($converted)->domain();
                    }
                }

                return $this
                    ->withFragment(null)
                    ->withQuery(null)
                    ->withPath('')
                    ->withUserInfo(null)
                    ->withHost($converted)
                    ->toString();
            }

            $components = UriString::parse($this->path);
            $scheme = strtolower($components['scheme'] ?? '');
            if (! (UriScheme::tryFrom($scheme)?->isWhatWgSpecial() ?? false)) {
                return null;
            }

            return self::fromComponents($components)->origin;
        } catch (UriException) {
            return null;
        }
    }

    /**
     * URI validation for URI schemes which allows only scheme and path components.
     */
    private function isUriWithSchemeAndPathOnly(): bool
    {
        return null === $this->authority
            && null === $this->query
            && null === $this->fragment;
    }

    /**
     * URI validation for URI schemes which allows only scheme, host and path components.
     */
    private function isUriWithSchemeHostAndPathOnly(): bool
    {
        return null === $this->userInfo
            && null === $this->port
            && null === $this->query
            && null === $this->fragment
            && !('' != $this->scheme && null === $this->host);
    }

    /**
     * URI validation for URI schemes which disallow the empty '' host.
     */
    private function isNonEmptyHostUri(): bool
    {
        return '' !== $this->host
            && !(null !== $this->scheme && null === $this->host);
    }

    /**
     * URI validation for URIs schemes which disallow the empty '' host
     * and forbids the fragment component.
     */
    private function isNonEmptyHostUriWithoutFragment(): bool
    {
        return $this->isNonEmptyHostUri() && null === $this->fragment;
    }

    /**
     * URI validation for URIs schemes which disallow the empty '' host
     * and forbids fragment and query components.
     */
    private function isNonEmptyHostUriWithoutFragmentAndQuery(): bool
    {
        return $this->isNonEmptyHostUri() && null === $this->fragment && null === $this->query;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Returns the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @see ::toString
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Returns the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     */
    public function toString(): string
    {
        return $this->toAsciiString();
    }

    /**
     * Returns the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     */
    public function toAsciiString(): string
    {
        return $this->uriAsciiString;
    }

    /**
     * Returns the string representation as a URI reference.
     *
     * The host is converted to its UNICODE representation if available
     */
    public function toUnicodeString(): string
    {
        return $this->uriUnicodeString;
    }

    /**
     * Returns the human-readable string representation of the URI as an IRI.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3987
     */
    public function toDisplayString(): string
    {
        return UriString::toIriString($this->toString());
    }

    /**
     * Returns the Unix filesystem path.
     *
     * The method will return null if a scheme is present and is not the `file` scheme
     */
    public function toUnixPath(): ?string
    {
        return match ($this->scheme) {
            'file', null => rawurldecode($this->path),
            default => null,
        };
    }

    /**
     * Returns the Windows filesystem path.
     *
     * The method will return null if a scheme is present and is not the `file` scheme
     */
    public function toWindowsPath(): ?string
    {
        static $regexpWindowsPath = ',^(?<root>[a-zA-Z]:),';

        if (!in_array($this->scheme, ['file', null], true)) {
            return null;
        }

        $originalPath = $this->path;
        $path = $originalPath;
        if ('/' === ($path[0] ?? '')) {
            $path = substr($path, 1);
        }

        if (1 === preg_match($regexpWindowsPath, $path, $matches)) {
            $root = $matches['root'];
            $path = substr($path, strlen($root));

            return $root.str_replace('/', '\\', rawurldecode($path));
        }

        $host = $this->host;

        return match (null) {
            $host => str_replace('/', '\\', rawurldecode($originalPath)),
            default => '\\\\'.$host.'\\'.str_replace('/', '\\', rawurldecode($path)),
        };
    }

    /**
     * Returns a string representation of a File URI according to RFC8089.
     *
     * The method will return null if the URI scheme is not the `file` scheme
     *
     * @see https://datatracker.ietf.org/doc/html/rfc8089
     */
    public function toRfc8089(): ?string
    {
        $path = $this->path;

        return match (true) {
            'file' !== $this->scheme => null,
            in_array($this->authority, ['', null, 'localhost'], true) => 'file:'.match (true) {
                '' === $path,
                '/' === $path[0] => $path,
                default => '/'.$path,
            },
            default => $this->toString(),
        };
    }

    /**
     * Save the data to a specific file.
     *
     * The method returns the number of bytes written to the file
     * or null for any other scheme except the data scheme
     *
     * @param SplFileInfo|SplFileObject|resource|Stringable|string $destination
     * @param ?resource $context
     *
     * @throws RuntimeException if the content cannot be stored.
     */
    public function toFileContents(mixed $destination, $context = null): ?int
    {
        if ('data' !== $this->scheme) {
            return null;
        }

        [$mediaType, $document] = explode(',', $this->path, 2) + [0 => '', 1 => null];
        null !== $document || throw new RuntimeException('Unable to extract the document part from the URI path.');

        $data = match (true) {
            str_ends_with((string) $mediaType, ';base64') => (string) base64_decode($document, true),
            default => rawurldecode($document),
        };

        $res = match (true) {
            $destination instanceof SplFileObject => $destination->fwrite($data),
            $destination instanceof SplFileInfo => $destination->openFile(mode:'wb', context: $context)->fwrite($data),
            is_resource($destination) => fwrite($destination, $data),
            $destination instanceof Stringable,
            is_string($destination) => (function () use ($destination, $data, $context): int|false {
                set_error_handler(fn (int $errno, string $errstr, string $errfile, int $errline) => true);
                $rsrc = fopen((string) $destination, mode:'wb', context: $context);
                if (false === $rsrc) {
                    restore_error_handler();
                    throw new RuntimeException('Unable to open the destination file: '.$destination);
                }

                $bytes = fwrite($rsrc, $data);
                fclose($rsrc);
                restore_error_handler();

                return $bytes;
            })(),
            default => throw new TypeError('Unsupported destination type; expected SplFileObject, SplFileInfo, resource or a string; '.(is_object($destination) ? $destination::class : gettype($destination)).' given.'),
        };

        false !== $res || throw new RuntimeException('Unable to write to the destination file.');

        return $res;
    }

    /**
     * Returns an associative array containing all the URI components.
     *
     * @return ComponentMap
     */
    public function toComponents(): array
    {
        return [
            'scheme' => $this->scheme,
            'user' => $this->user,
            'pass' => $this->pass,
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->path,
            'query' => $this->query,
            'fragment' => $this->fragment,
        ];
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function getAuthority(): ?string
    {
        return $this->authority;
    }

    /**
     * Returns the user component encoded value.
     *
     * @see https://wiki.php.net/rfc/url_parsing_api
     */
    public function getUsername(): ?string
    {
        return $this->user;
    }

    public function getPassword(): ?string
    {
        return $this->pass;
    }

    public function getUserInfo(): ?string
    {
        return $this->userInfo;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getUnicodeHost(): ?string
    {
        if (null === $this->host) {
            return null;
        }

        $host = IdnaConverter::toUnicode($this->host)->domain();
        if ($host === $this->host) {
            return $this->host;
        }

        return $host;
    }

    public function isIpv4Host(): bool
    {
        return HostRecord::isIpv4($this->host);
    }

    public function isIpv6Host(): bool
    {
        return HostRecord::isIpv6($this->host);
    }

    public function isIpvFutureHost(): bool
    {
        return HostRecord::isIpvFuture($this->host);
    }

    public function isIpHost(): bool
    {
        return HostRecord::isIp($this->host);
    }

    public function isRegisteredNameHost(): bool
    {
        return HostRecord::isRegisteredName($this->host);
    }

    public function isDomainHost(): bool
    {
        return HostRecord::isDomain($this->host);
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function when(callable|bool $condition, callable $onSuccess, ?callable $onFail = null): static
    {
        if (!is_bool($condition)) {
            $condition = $condition($this);
        }

        return match (true) {
            $condition => $onSuccess($this),
            null !== $onFail => $onFail($this),
            default => $this,
        } ?? $this;
    }

    public function transform(callable $callback): static
    {
        return $callback($this);
    }

    public function withScheme(BackedEnum|Stringable|string|null $scheme): static
    {
        $scheme = $this->formatScheme($this->filterString($scheme));

        return match ($scheme) {
            $this->scheme => $this,
            default => new self($scheme, $this->user, $this->pass, $this->host, $this->port, $this->path, $this->query, $this->fragment),
        };
    }

    /**
     * Filter a string.
     *
     * @throws SyntaxError if the submitted data cannot be converted to string
     */
    private function filterString(BackedEnum|Stringable|string|null $str): ?string
    {
        $str = match (true) {
            $str instanceof FragmentDirective => $str->toFragmentValue(),
            $str instanceof UriComponentInterface => $str->value(),
            $str instanceof BackedEnum => (string) $str->value,
            null === $str => null,
            default => (string) $str,
        };

        return match (true) {
            null === $str => null,
            1 === preg_match(self::REGEXP_INVALID_CHARS, $str) => throw new SyntaxError('The component `'.$str.'` contains invalid characters.'),
            default => $str,
        };
    }

    public function withUserInfo(
        BackedEnum|Stringable|string|null $user,
        #[SensitiveParameter] BackedEnum|Stringable|string|null $password = null
    ): static {
        $user = Encoder::encodeUser($this->filterString($user));
        $pass = Encoder::encodePassword($this->filterString($password));
        $userInfo = $user;
        if (null !== $password) {
            $userInfo .= ':'.$pass;
        }

        return match ($userInfo) {
            $this->userInfo => $this,
            default => new self($this->scheme, $user, $pass, $this->host, $this->port, $this->path, $this->query, $this->fragment),
        };
    }

    public function withUsername(BackedEnum|Stringable|string|null $user): static
    {
        return $this->withUserInfo($user, $this->pass);
    }

    public function withPassword(#[SensitiveParameter] BackedEnum|Stringable|string|null $password): static
    {
        return $this->withUserInfo($this->user, $password);
    }

    public function withHost(BackedEnum|Stringable|string|null $host): static
    {
        $host = $this->formatHost($this->filterString($host));

        return match ($host) {
            $this->host => $this,
            default => new self($this->scheme, $this->user, $this->pass, $host, $this->port, $this->path, $this->query, $this->fragment),
        };
    }

    public function withPort(BackedEnum|int|null $port): static
    {
        $port = $this->formatPort($port);

        return match ($port) {
            $this->port => $this,
            default => new self($this->scheme, $this->user, $this->pass, $this->host, $port, $this->path, $this->query, $this->fragment),
        };
    }

    public function withPath(BackedEnum|Stringable|string $path): static
    {
        $path = $this->formatPath($this->filterString($path) ?? throw new SyntaxError('The path component cannot be null.'));

        return match ($path) {
            $this->path => $this,
            default => new self($this->scheme, $this->user, $this->pass, $this->host, $this->port, $path, $this->query, $this->fragment),
        };
    }

    public function withQuery(BackedEnum|Stringable|string|null $query): static
    {
        $query = Encoder::encodeQueryOrFragment($this->filterString($query));

        return match ($query) {
            $this->query => $this,
            default => new self($this->scheme, $this->user, $this->pass, $this->host, $this->port, $this->path, $query, $this->fragment),
        };
    }

    public function withFragment(BackedEnum|Stringable|string|null $fragment): static
    {
        $fragment = Encoder::encodeQueryOrFragment($this->filterString($fragment));

        return match ($fragment) {
            $this->fragment => $this,
            default => new self($this->scheme, $this->user, $this->pass, $this->host, $this->port, $this->path, $this->query, $fragment),
        };
    }

    /**
     * Tells whether the `file` scheme base URI represents a local file.
     */
    public function isLocalFile(): bool
    {
        return match (true) {
            'file' !== $this->scheme => false,
            in_array($this->authority, ['', null, 'localhost'], true) => true,
            default => false,
        };
    }

    /**
     * Tells whether the URI is opaque or not.
     *
     * A URI is opaque if and only if it is absolute
     * and does not have an authority path.
     */
    public function isOpaque(): bool
    {
        return null === $this->authority
            && null !== $this->scheme;
    }

    /**
     * Tells whether two URI do not share the same origin.
     */
    public function isCrossOrigin(Rfc3986Uri|WhatWgUrl|Urn|Stringable|string $uri): bool
    {
        if (null === $this->origin) {
            return true;
        }

        $uri = self::tryNew($uri);
        if (null === $uri || null === ($origin = $uri->getOrigin())) {
            return true;
        }

        return $this->origin !== $origin;
    }

    public function isSameOrigin(Rfc3986Uri|WhatWgUrl|Urn|Stringable|string $uri): bool
    {
        return ! $this->isCrossOrigin($uri);
    }

    /**
     * Tells whether the URI is absolute.
     */
    public function isAbsolute(): bool
    {
        return null !== $this->scheme;
    }

    /**
     * Tells whether the URI is a network path.
     */
    public function isNetworkPath(): bool
    {
        return null === $this->scheme
            && null !== $this->authority;
    }

    /**
     * Tells whether the URI is an absolute path.
     */
    public function isAbsolutePath(): bool
    {
        return null === $this->scheme
            && null === $this->authority
            && '/' === ($this->path[0] ?? '');
    }

    /**
     * Tells whether the URI is a relative path.
     */
    public function isRelativePath(): bool
    {
        return null === $this->scheme
            && null === $this->authority
            && '/' !== ($this->path[0] ?? '');
    }

    /**
     * Tells whether both URIs refer to the same document.
     */
    public function isSameDocument(Rfc3986Uri|WhatWgUrl|UriInterface|Stringable|Urn|string $uri): bool
    {
        return $this->equals($uri);
    }

    public function equals(Rfc3986Uri|WhatWgUrl|UriInterface|Stringable|Urn|string $uri, UriComparisonMode $uriComparisonMode = UriComparisonMode::ExcludeFragment): bool
    {
        if (!$uri instanceof UriInterface && !$uri instanceof Rfc3986Uri && !$uri instanceof WhatWgUrl) {
            $uri = self::tryNew($uri);
        }

        if (null === $uri) {
            return false;
        }

        $baseUri = $this;
        if (UriComparisonMode::ExcludeFragment === $uriComparisonMode) {
            $uri = $uri->withFragment(null);
            $baseUri = $baseUri->withFragment(null);
        }

        return $baseUri->normalize()->toString() === match (true) {
            $uri instanceof Rfc3986Uri => $uri->toString(),
            $uri instanceof WhatWgUrl => $uri->toAsciiString(),
            default => $uri->normalize()->toString(),
        };
    }

    /**
     * Normalize a URI by applying non-destructive and destructive normalization
     * rules as defined in RFC3986 and RFC3987.
     */
    public function normalize(): static
    {
        $uriString = $this->toString();
        if ('' === $uriString) {
            return $this;
        }

        $normalizedUriString = UriString::normalize($uriString);
        $normalizedUri = self::new($normalizedUriString);
        if (null !== $normalizedUri->getAuthority() && ('' === $normalizedUri->getPath() && (UriScheme::tryFrom($normalizedUri->getScheme() ?? '')?->isWhatWgSpecial() ?? false))) {
            $normalizedUri = $normalizedUri->withPath('/');
        }

        if ($normalizedUri->toString() === $uriString) {
            return $this;
        }

        return $normalizedUri;
    }

    /**
     * Resolves a URI against a base URI using RFC3986 rules.
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * a URI instance of the same type that contains the applied modifications.
     *
     * This method MUST be transparent when dealing with errors and exceptions.
     * It MUST not alter or silence them apart from validating its own parameters.
     */
    public function resolve(Rfc3986Uri|WhatWgUrl|UriInterface|Stringable|Urn|BackedEnum|string $uri): static
    {
        return self::new(UriString::resolve(
            match (true) {
                $uri instanceof UriInterface,
                $uri instanceof Rfc3986Uri => $uri->toString(),
                $uri instanceof WhatWgUrl => $uri->toAsciiString(),
                $uri instanceof BackedEnum => (string) $uri->value,
                default => $uri,
            },
            $this->toString()
        ));
    }

    /**
     * Relativize a URI according to a base URI.
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * a URI instance of the same type that contains the applied modifications.
     *
     * This method MUST be transparent when dealing with error and exceptions.
     * It MUST not alter of silence them apart from validating its own parameters.
     */
    public function relativize(Rfc3986Uri|WhatWgUrl|UriInterface|Stringable|Urn|BackedEnum|string $uri): static
    {
        $uri = self::new($uri);

        if (
            $this->scheme !== $uri->getScheme() ||
            $this->authority !== $uri->getAuthority() ||
            $uri->isRelativePath()) {
            return $uri;
        }

        $targetPath = $uri->getPath();
        $basePath = $this->path;

        $uri = $uri
            ->withScheme(null)
            ->withUserInfo(null)
            ->withPort(null)
            ->withHost(null);

        return match (true) {
            $targetPath !== $basePath => $uri->withPath(self::relativizePath($targetPath, $basePath)),
            $this->query === $uri->getQuery() => $uri->withPath('')->withQuery(null),
            null === $uri->getQuery() => $uri->withPath(self::formatPathWithEmptyBaseQuery($targetPath)),
            default => $uri->withPath(''),
        };
    }

    /**
     * Formatting the path to keep a resolvable URI.
     */
    private static function formatPathWithEmptyBaseQuery(string $path): string
    {
        $targetSegments = self::getSegments($path);
        $basename = $targetSegments[array_key_last($targetSegments)];

        return '' === $basename ? './' : $basename;
    }

    /**
     * Relatives the URI for an authority-less target URI.
     */
    private static function relativizePath(string $path, string $basePath): string
    {
        $baseSegments = self::getSegments($basePath);
        $targetSegments = self::getSegments($path);
        $targetBasename = array_pop($targetSegments);
        array_pop($baseSegments);
        foreach ($baseSegments as $offset => $segment) {
            if (!isset($targetSegments[$offset]) || $segment !== $targetSegments[$offset]) {
                break;
            }
            unset($baseSegments[$offset], $targetSegments[$offset]);
        }
        $targetSegments[] = $targetBasename;

        return static::formatRelativePath(
            str_repeat('../', count($baseSegments)).implode('/', $targetSegments),
            $basePath
        );
    }

    /**
     * Formatting the path to keep a valid URI.
     */
    private static function formatRelativePath(string $path, string $basePath): string
    {
        $colonPosition = strpos($path, ':');
        $slashPosition = strpos($path, '/');

        return match (true) {
            '' === $path => match (true) {
                '' === $basePath,
                '/' === $basePath => $basePath,
                default => './',
            },
            false === $colonPosition => $path,
            false === $slashPosition,
            $colonPosition < $slashPosition  =>  "./$path",
            default => $path,
        };
    }

    /**
     * returns the path segments.
     *
     * @return array<string>
     */
    private static function getSegments(string $path): array
    {
        return explode('/', match (true) {
            '' === $path,
            '/' !== $path[0] => $path,
            default => substr($path, 1),
        });
    }

    /**
     * @return ComponentMap
     */
    public function __debugInfo(): array
    {
        return $this->toComponents();
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.6.0
     * @codeCoverageIgnore
     * @see Uri::parse()
     *
     * Creates a new instance from a URI and a Base URI.
     *
     * The returned URI must be absolute.
     */
    #[Deprecated(message:'use League\Uri\Uri::parse() instead', since:'league/uri:7.6.0')]
    public static function fromBaseUri(WhatWgUrl|Rfc3986Uri|Stringable|string $uri, WhatWgUrl|Rfc3986Uri|Stringable|string|null $baseUri = null): self
    {
        $formatter = fn (WhatWgUrl|Rfc3986Uri|Stringable|string $uri): string => match (true) {
            $uri instanceof Rfc3986Uri => $uri->toRawString(),
            $uri instanceof WhatWgUrl => $uri->toAsciiString(),
            default => str_replace(' ', '%20', (string) $uri),
        };

        return self::new(
            UriString::resolve(
                uri: $formatter($uri),
                baseUri: null !== $baseUri ? $formatter($baseUri) : $baseUri
            )
        );
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.5.0
     * @codeCoverageIgnore
     * @see Uri::toComponents()
     *
     * @return ComponentMap
     */
    #[Deprecated(message:'use League\Uri\Uri::toComponents() instead', since:'league/uri:7.5.0')]
    public function getComponents(): array
    {
        return $this->toComponents();
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Uri::new()
     */
    #[Deprecated(message:'use League\Uri\Uri::new() instead', since:'league/uri:7.0.0')]
    public static function createFromString(Stringable|string $uri = ''): self
    {
        return self::new($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Uri::fromComponents()
     *
     * @param InputComponentMap $components a hash representation of the URI similar to PHP parse_url function result
     */
    #[Deprecated(message:'use League\Uri\Uri::fromComponents() instead', since:'league/uri:7.0.0')]
    public static function createFromComponents(array $components = []): self
    {
        return self::fromComponents($components);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @param resource|null $context
     *
     * @throws MissingFeature If ext/fileinfo is not installed
     * @throws SyntaxError If the file does not exist or is not readable
     * @see Uri::fromFileContents()
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     */
    #[Deprecated(message:'use League\Uri\Uri::fromDataPath() instead', since:'league/uri:7.0.0')]
    public static function createFromDataPath(string $path, $context = null): self
    {
        return self::fromFileContents($path, $context);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Uri::fromBaseUri()
     *
     * Creates a new instance from a URI and a Base URI.
     *
     * The returned URI must be absolute.
     */
    #[Deprecated(message:'use League\Uri\Uri::fromBaseUri() instead', since:'league/uri:7.0.0')]
    public static function createFromBaseUri(
        Stringable|UriInterface|String $uri,
        Stringable|UriInterface|String|null $baseUri = null
    ): static {
        return self::fromBaseUri($uri, $baseUri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Uri::fromUnixPath()
     *
     * Create a new instance from a Unix path string.
     */
    #[Deprecated(message:'use League\Uri\Uri::fromUnixPath() instead', since:'league/uri:7.0.0')]
    public static function createFromUnixPath(string $uri = ''): self
    {
        return self::fromUnixPath($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Uri::fromWindowsPath()
     *
     * Create a new instance from a local Windows path string.
     */
    #[Deprecated(message:'use League\Uri\Uri::fromWindowsPath() instead', since:'league/uri:7.0.0')]
    public static function createFromWindowsPath(string $uri = ''): self
    {
        return self::fromWindowsPath($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Uri::new()
     *
     * Create a new instance from a URI object.
     */
    #[Deprecated(message:'use League\Uri\Uri::new() instead', since:'league/uri:7.0.0')]
    public static function createFromUri(Psr7UriInterface|UriInterface $uri): self
    {
        return self::new($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Uri::fromServer()
     *
     * Create a new instance from the environment.
     */
    #[Deprecated(message:'use League\Uri\Uri::fromServer() instead', since:'league/uri:7.0.0')]
    public static function createFromServer(array $server): self
    {
        return self::fromServer($server);
    }
}
