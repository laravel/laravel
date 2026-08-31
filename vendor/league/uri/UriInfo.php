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
use League\Uri\Contracts\UriInterface;
use Psr\Http\Message\UriInterface as Psr7UriInterface;

/**
 * @deprecated since version 7.0.0
 * @codeCoverageIgnore
 * @see BaseUri
 */
final class UriInfo
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Tells whether the URI represents an absolute URI.
     */
    #[Deprecated(message:'use League\Uri\BaseUri::isAbsolute() instead', since:'league/uri:7.0.0')]
    public static function isAbsolute(Psr7UriInterface|UriInterface $uri): bool
    {
        return BaseUri::from($uri)->isAbsolute();
    }

    /**
     * Tell whether the URI represents a network path.
     */
    #[Deprecated(message:'use League\Uri\BaseUri::isNetworkPath() instead', since:'league/uri:7.0.0')]
    public static function isNetworkPath(Psr7UriInterface|UriInterface $uri): bool
    {
        return BaseUri::from($uri)->isNetworkPath();
    }

    /**
     * Tells whether the URI represents an absolute path.
     */
    #[Deprecated(message:'use League\Uri\BaseUri::isAbsolutePath() instead', since:'league/uri:7.0.0')]
    public static function isAbsolutePath(Psr7UriInterface|UriInterface $uri): bool
    {
        return BaseUri::from($uri)->isAbsolutePath();
    }

    /**
     * Tell whether the URI represents a relative path.
     *
     */
    #[Deprecated(message:'use League\Uri\BaseUri::isRelativePath() instead', since:'league/uri:7.0.0')]
    public static function isRelativePath(Psr7UriInterface|UriInterface $uri): bool
    {
        return BaseUri::from($uri)->isRelativePath();
    }

    /**
     * Tells whether both URI refers to the same document.
     */
    #[Deprecated(message:'use League\Uri\BaseUri::isSameDocument() instead', since:'league/uri:7.0.0')]
    public static function isSameDocument(Psr7UriInterface|UriInterface $uri, Psr7UriInterface|UriInterface $baseUri): bool
    {
        return BaseUri::from($baseUri)->isSameDocument($uri);
    }

    /**
     * Returns the URI origin property as defined by WHATWG URL living standard.
     *
     * {@see https://url.spec.whatwg.org/#origin}
     *
     * For URI without a special scheme the method returns null
     * For URI with the file scheme the method will return null (as this is left to the implementation decision)
     * For URI with a special scheme the method returns the scheme followed by its authority (without the userinfo part)
     */
    #[Deprecated(message:'use League\Uri\BaseUri::origin() instead', since:'league/uri:7.0.0')]
    public static function getOrigin(Psr7UriInterface|UriInterface $uri): ?string
    {
        return BaseUri::from($uri)->origin()?->__toString();
    }

    /**
     * Tells whether two URI do not share the same origin.
     *
     * @see UriInfo::getOrigin()
     */
    #[Deprecated(message:'use League\Uri\BaseUri::isCrossOrigin() instead', since:'league/uri:7.0.0')]
    public static function isCrossOrigin(Psr7UriInterface|UriInterface $uri, Psr7UriInterface|UriInterface $baseUri): bool
    {
        return BaseUri::from($baseUri)->isCrossOrigin($uri);
    }
}
