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

use Deprecated;
use JsonSerializable;
use League\Uri\Contracts\UriAccess;
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\MissingFeature;
use League\Uri\Idna\Converter as IdnaConverter;
use League\Uri\IPv4\Converter as IPv4Converter;
use League\Uri\IPv6\Converter as IPv6Converter;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface as Psr7UriInterface;
use Stringable;

use function array_pop;
use function array_reduce;
use function count;
use function explode;
use function implode;
use function in_array;
use function preg_match;
use function rawurldecode;
use function sort;
use function str_contains;
use function str_repeat;
use function str_replace;
use function strpos;
use function substr;

/**
 * @phpstan-import-type ComponentMap from UriInterface
 * @deprecated since version 7.6.0
 *
 * @see Modifier
 * @see Uri
 */
class BaseUri implements Stringable, JsonSerializable, UriAccess
{
    /** @var array<string,int> */
    final protected const WHATWG_SPECIAL_SCHEMES = ['ftp' => 1, 'http' => 1, 'https' => 1, 'ws' => 1, 'wss' => 1];

    /** @var array<string,int> */
    final protected const DOT_SEGMENTS = ['.' => 1, '..' => 1];

    protected readonly Psr7UriInterface|UriInterface|null $origin;
    protected readonly ?string $nullValue;

    /**
     * @param UriFactoryInterface|null $uriFactory Deprecated, will be removed in the next major release
     */
    final protected function __construct(
        protected readonly Psr7UriInterface|UriInterface $uri,
        protected readonly ?UriFactoryInterface $uriFactory
    ) {
        $this->nullValue = $this->uri instanceof Psr7UriInterface ? '' : null;
        $this->origin = $this->computeOrigin($this->uri, $this->nullValue);
    }

    public static function from(Stringable|string $uri, ?UriFactoryInterface $uriFactory = null): static
    {
        $uri = static::formatHost(static::filterUri($uri, $uriFactory));
        return new static($uri, $uriFactory);
    }

    public function withUriFactory(UriFactoryInterface $uriFactory): static
    {
        return new static($this->uri, $uriFactory);
    }

    public function withoutUriFactory(): static
    {
        return new static($this->uri, null);
    }

    public function getUri(): Psr7UriInterface|UriInterface
    {
        return $this->uri;
    }

    public function getUriString(): string
    {
        return $this->uri->__toString();
    }

    public function jsonSerialize(): string
    {
        return $this->uri->__toString();
    }

    public function __toString(): string
    {
        return $this->uri->__toString();
    }

    public function origin(): ?self
    {
        return match (null) {
            $this->origin => null,
            default => new self($this->origin, $this->uriFactory),
        };
    }

    /**
     * Returns the Unix filesystem path.
     *
     * The method will return null if a scheme is present and is not the `file` scheme
     */
    public function unixPath(): ?string
    {
        return match ($this->uri->getScheme()) {
            'file', $this->nullValue => rawurldecode($this->uri->getPath()),
            default => null,
        };
    }

    /**
     * Returns the Windows filesystem path.
     *
     * The method will return null if a scheme is present and is not the `file` scheme
     */
    public function windowsPath(): ?string
    {
        static $regexpWindowsPath = ',^(?<root>[a-zA-Z]:),';

        if (!in_array($this->uri->getScheme(), ['file', $this->nullValue], true)) {
            return null;
        }

        $originalPath = $this->uri->getPath();
        $path = $originalPath;
        if ('/' === ($path[0] ?? '')) {
            $path = substr($path, 1);
        }

        if (1 === preg_match($regexpWindowsPath, $path, $matches)) {
            $root = $matches['root'];
            $path = substr($path, strlen($root));

            return $root.str_replace('/', '\\', rawurldecode($path));
        }

        $host = $this->uri->getHost();

        return match ($this->nullValue) {
            $host => str_replace('/', '\\', rawurldecode($originalPath)),
            default => '\\\\'.$host.'\\'.str_replace('/', '\\', rawurldecode($path)),
        };
    }

    /**
     * Returns a string representation of a File URI according to RFC8089.
     *
     * The method will return null if the URI scheme is not the `file` scheme
     */
    public function toRfc8089(): ?string
    {
        $path = $this->uri->getPath();

        return match (true) {
            'file' !== $this->uri->getScheme() => null,
            in_array($this->uri->getAuthority(), ['', null, 'localhost'], true) => 'file:'.match (true) {
                '' === $path,
                '/' === $path[0] => $path,
                default => '/'.$path,
            },
            default => (string) $this->uri,
        };
    }

    /**
     * Tells whether the `file` scheme base URI represents a local file.
     */
    public function isLocalFile(): bool
    {
        return match (true) {
            'file' !== $this->uri->getScheme() => false,
            in_array($this->uri->getAuthority(), ['', null, 'localhost'], true) => true,
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
        return $this->nullValue === $this->uri->getAuthority()
            && $this->isAbsolute();
    }

    /**
     * Tells whether two URI do not share the same origin.
     */
    public function isCrossOrigin(Stringable|string $uri): bool
    {
        if (null === $this->origin) {
            return true;
        }

        $uri = static::filterUri($uri);
        $uriOrigin = $this->computeOrigin($uri, $uri instanceof Psr7UriInterface ? '' : null);

        return match(true) {
            null === $uriOrigin,
            $uriOrigin->__toString() !== $this->origin->__toString() => true,
            default => false,
        };
    }

    /**
     * Tells whether the URI is absolute.
     */
    public function isAbsolute(): bool
    {
        return $this->nullValue !== $this->uri->getScheme();
    }

    /**
     * Tells whether the URI is a network path.
     */
    public function isNetworkPath(): bool
    {
        return $this->nullValue === $this->uri->getScheme()
            && $this->nullValue !== $this->uri->getAuthority();
    }

    /**
     * Tells whether the URI is an absolute path.
     */
    public function isAbsolutePath(): bool
    {
        return $this->nullValue === $this->uri->getScheme()
            && $this->nullValue === $this->uri->getAuthority()
            && '/' === ($this->uri->getPath()[0] ?? '');
    }

    /**
     * Tells whether the URI is a relative path.
     */
    public function isRelativePath(): bool
    {
        return $this->nullValue === $this->uri->getScheme()
            && $this->nullValue === $this->uri->getAuthority()
            && '/' !== ($this->uri->getPath()[0] ?? '');
    }

    /**
     * Tells whether both URI refers to the same document.
     */
    public function isSameDocument(Stringable|string $uri): bool
    {
        return self::normalizedUri($this->uri)->equals(self::normalizedUri($uri));
    }

    private static function normalizedUri(Stringable|string $uri): Uri
    {
        // Normalize the URI according to RFC3986
        $uri = ($uri instanceof Uri ? $uri : Uri::new($uri))->normalize();

        return $uri
            //Normalization as per WHATWG URL standard
            //only meaningful for WHATWG Special URI scheme protocol
            ->when(
                condition: '' === $uri->getPath() && null !== $uri->getAuthority(),
                onSuccess: fn (Uri $uri) => $uri->withPath('/'),
            )
            //Sorting as per WHATWG URLSearchParams class
            //not included on any equivalence algorithm
            ->when(
                condition: null !== ($query = $uri->getQuery()) && str_contains($query, '&'),
                onSuccess: function (Uri $uri) use ($query) {
                    $pairs = explode('&', (string) $query);
                    sort($pairs);

                    return $uri->withQuery(implode('&', $pairs));
                }
            );
    }

    /**
     * Tells whether the URI contains an Internationalized Domain Name (IDN).
     */
    public function hasIdn(): bool
    {
        return IdnaConverter::isIdn($this->uri->getHost());
    }

    /**
     * Tells whether the URI contains an IPv4 regardless if it is mapped or native.
     */
    public function hasIPv4(): bool
    {
        return IPv4Converter::fromEnvironment()->isIpv4($this->uri->getHost());
    }

    /**
     * Resolves a URI against a base URI using RFC3986 rules.
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * a URI instance of the same type that contains the applied modifications.
     *
     * This method MUST be transparent when dealing with error and exceptions.
     * It MUST not alter or silence them apart from validating its own parameters.
     */
    public function resolve(Stringable|string $uri): static
    {
        $resolved = UriString::resolve($uri, $this->uri);

        return new static(match ($this->uriFactory) {
            null => Uri::new($resolved),
            default => $this->uriFactory->createUri($resolved),
        }, $this->uriFactory);
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
    public function relativize(Stringable|string $uri): static
    {
        $uri = static::formatHost(static::filterUri($uri, $this->uriFactory));
        if ($this->canNotBeRelativize($uri)) {
            return new static($uri, $this->uriFactory);
        }

        $null = $uri instanceof Psr7UriInterface ? '' : null;
        $uri = $uri->withScheme($null)->withPort(null)->withUserInfo($null)->withHost($null);
        $targetPath = $uri->getPath();
        $basePath = $this->uri->getPath();

        return new static(
            match (true) {
                $targetPath !== $basePath => $uri->withPath(static::relativizePath($targetPath, $basePath)),
                static::componentEquals('query', $uri) => $uri->withPath('')->withQuery($null),
                $null === $uri->getQuery() => $uri->withPath(static::formatPathWithEmptyBaseQuery($targetPath)),
                default => $uri->withPath(''),
            },
            $this->uriFactory
        );
    }

    final protected function computeOrigin(Psr7UriInterface|UriInterface $uri, ?string $nullValue): Psr7UriInterface|UriInterface|null
    {
        if ($uri instanceof Uri) {
            $origin = $uri->getOrigin();
            if (null === $origin) {
                return null;
            }

            return Uri::tryNew($origin);
        }

        $origin = Uri::tryNew($uri)?->getOrigin();
        if (null === $origin) {
            return null;
        }

        $components = UriString::parse($origin);

        return $uri
                ->withFragment($nullValue)
                ->withQuery($nullValue)
                ->withPath('')
                ->withScheme('localhost')
                ->withHost((string) $components['host'])
                ->withPort($components['port'])
                ->withScheme((string) $components['scheme'])
                ->withUserInfo($nullValue);
    }

    /**
     * Input URI normalization to allow Stringable and string URI.
     */
    final protected static function filterUri(Stringable|string $uri, UriFactoryInterface|null $uriFactory = null): Psr7UriInterface|UriInterface
    {
        return match (true) {
            $uri instanceof UriAccess => $uri->getUri(),
            $uri instanceof Psr7UriInterface,
            $uri instanceof UriInterface => $uri,
            $uriFactory instanceof UriFactoryInterface => $uriFactory->createUri((string) $uri),
            default => Uri::new($uri),
        };
    }

    /**
     * Tells whether the component value from both URI object equals.
     *
     * @pqram 'query'|'authority'|'scheme' $property
     */
    final protected function componentEquals(string $property, Psr7UriInterface|UriInterface $uri): bool
    {
        $getComponent = function (string $property, Psr7UriInterface|UriInterface $uri): ?string {
            $component = match ($property) {
                'query' => $uri->getQuery(),
                'authority' => $uri->getAuthority(),
                default => $uri->getScheme(),
            };

            return match (true) {
                $uri instanceof UriInterface, '' !== $component => $component,
                default => null,
            };
        };

        return $getComponent($property, $uri) === $getComponent($property, $this->uri);
    }

    /**
     * Filter the URI object.
     */
    final protected static function formatHost(Psr7UriInterface|UriInterface $uri): Psr7UriInterface|UriInterface
    {
        $host = $uri->getHost();
        try {
            $converted = IPv4Converter::fromEnvironment()->toDecimal($host);
        } catch (MissingFeature) {
            $converted = null;
        }

        if (false === filter_var($converted, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $converted = IPv6Converter::compress($host);
        }

        return match (true) {
            null !== $converted => $uri->withHost($converted),
            '' === $host,
            $uri instanceof UriInterface => $uri,
            default => $uri->withHost((string) Uri::fromComponents(['host' => $host])->getHost()),
        };
    }

    /**
     * Tells whether the submitted URI object can be relativized.
     */
    final protected function canNotBeRelativize(Psr7UriInterface|UriInterface $uri): bool
    {
        return !static::componentEquals('scheme', $uri)
            || !static::componentEquals('authority', $uri)
            || static::from($uri)->isRelativePath();
    }

    /**
     * Relatives the URI for an authority-less target URI.
     */
    final protected static function relativizePath(string $path, string $basePath): string
    {
        $baseSegments = static::getSegments($basePath);
        $targetSegments = static::getSegments($path);
        $targetBasename = array_pop($targetSegments);
        array_pop($baseSegments);
        foreach ($baseSegments as $offset => $segment) {
            if (!isset($targetSegments[$offset]) || $segment !== $targetSegments[$offset]) {
                break;
            }
            unset($baseSegments[$offset], $targetSegments[$offset]);
        }
        $targetSegments[] = $targetBasename;

        return static::formatPath(
            str_repeat('../', count($baseSegments)).implode('/', $targetSegments),
            $basePath
        );
    }

    /**
     * returns the path segments.
     *
     * @return string[]
     */
    final protected static function getSegments(string $path): array
    {
        return explode('/', match (true) {
            '' === $path,
            '/' !== $path[0] => $path,
            default => substr($path, 1),
        });
    }

    /**
     * Formatting the path to keep a valid URI.
     */
    final protected static function formatPath(string $path, string $basePath): string
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
     * Formatting the path to keep a resolvable URI.
     */
    final protected static function formatPathWithEmptyBaseQuery(string $path): string
    {
        $targetSegments = static::getSegments($path);
        $basename = $targetSegments[array_key_last($targetSegments)];

        return '' === $basename ? './' : $basename;
    }

    /**
     * Normalizes a URI for comparison; this URI string representation is not suitable for usage as per RFC guidelines.
     *
     * @deprecated since version 7.6.0
     *
     * @codeCoverageIgnore
     */
    #[Deprecated(message:'no longer used by the isSameDocument method', since:'league/uri-interfaces:7.6.0')]
    final protected function normalize(Psr7UriInterface|UriInterface $uri): string
    {
        $newUri = $uri->withScheme($uri instanceof Psr7UriInterface ? '' : null);
        if ('' === $newUri->__toString()) {
            return '';
        }

        return UriString::normalize($newUri);
    }


    /**
     * Remove dot segments from the URI path as per RFC specification.
     *
     * @deprecated since version 7.6.0
     *
     * @codeCoverageIgnore
     */
    #[Deprecated(message:'no longer used by the isSameDocument method', since:'league/uri-interfaces:7.6.0')]
    final protected function removeDotSegments(string $path): string
    {
        if (!str_contains($path, '.')) {
            return $path;
        }

        $reducer = function (array $carry, string $segment): array {
            if ('..' === $segment) {
                array_pop($carry);

                return $carry;
            }

            if (!isset(static::DOT_SEGMENTS[$segment])) {
                $carry[] = $segment;
            }

            return $carry;
        };

        $oldSegments = explode('/', $path);
        $newPath = implode('/', array_reduce($oldSegments, $reducer(...), []));
        if (isset(static::DOT_SEGMENTS[$oldSegments[array_key_last($oldSegments)]])) {
            $newPath .= '/';
        }

        // @codeCoverageIgnoreStart
        // added because some PSR-7 implementations do not respect RFC3986
        if (str_starts_with($path, '/') && !str_starts_with($newPath, '/')) {
            return '/'.$newPath;
        }
        // @codeCoverageIgnoreEnd

        return $newPath;
    }

    /**
     * Resolves an URI path and query component.
     *
     * @return array{0:string, 1:string|null}
     *
     * @deprecated since version 7.6.0
     *
     * @codeCoverageIgnore
     */
    #[Deprecated(message:'no longer used by the isSameDocument method', since:'league/uri-interfaces:7.6.0')]
    final protected function resolvePathAndQuery(Psr7UriInterface|UriInterface $uri): array
    {
        $targetPath = $uri->getPath();
        $null = $uri instanceof Psr7UriInterface ? '' : null;

        if (str_starts_with($targetPath, '/')) {
            return [$targetPath, $uri->getQuery()];
        }

        if ('' === $targetPath) {
            $targetQuery = $uri->getQuery();
            if ($null === $targetQuery) {
                $targetQuery = $this->uri->getQuery();
            }

            $targetPath = $this->uri->getPath();
            //@codeCoverageIgnoreStart
            //because some PSR-7 Uri implementations allow this RFC3986 forbidden construction
            if (null !== $this->uri->getAuthority() && !str_starts_with($targetPath, '/')) {
                $targetPath = '/'.$targetPath;
            }
            //@codeCoverageIgnoreEnd

            return [$targetPath, $targetQuery];
        }

        $basePath = $this->uri->getPath();
        if (null !== $this->uri->getAuthority() && '' === $basePath) {
            $targetPath = '/'.$targetPath;
        }

        if ('' !== $basePath) {
            $segments = explode('/', $basePath);
            array_pop($segments);
            if ([] !== $segments) {
                $targetPath = implode('/', $segments).'/'.$targetPath;
            }
        }

        return [$targetPath, $uri->getQuery()];
    }
}
