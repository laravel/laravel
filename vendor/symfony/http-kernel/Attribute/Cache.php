<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Attribute;

/**
 * Describes the default HTTP cache headers on controllers.
 * Headers defined in the Cache attribute are ignored if they are already set
 * by the controller.
 *
 * @see https://symfony.com/doc/current/http_cache.html#making-your-responses-http-cacheable
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class Cache
{
    public function __construct(
        /**
         * The expiration date as a valid date for the strtotime() function.
         */
        public ?string $expires = null,

        /**
         * The number of seconds that the response is considered fresh by a private
         * cache like a web browser.
         */
        public int|string|null $maxage = null,

        /**
         * The number of seconds that the response is considered fresh by a public
         * cache like a reverse proxy cache.
         */
        public int|string|null $smaxage = null,

        /**
         * If true, the contents will be stored in a public cache and served to all
         * the next requests.
         */
        public ?bool $public = null,

        /**
         * If true, the response is not served stale by a cache in any circumstance
         * without first revalidating with the origin.
         */
        public bool $mustRevalidate = false,

        /**
         * Set "Vary" header.
         *
         * Example:
         * ['Accept-Encoding', 'User-Agent']
         *
         * @see https://symfony.com/doc/current/http_cache/cache_vary.html
         *
         * @var string[]
         */
        public array $vary = [],

        /**
         * An expression to compute the Last-Modified HTTP header.
         *
         * The expression is evaluated by the ExpressionLanguage component, it
         * receives all the request attributes and the resolved controller arguments.
         *
         * The result of the expression must be a DateTimeInterface.
         */
        public ?string $lastModified = null,

        /**
         * An expression to compute the ETag HTTP header.
         *
         * The expression is evaluated by the ExpressionLanguage component, it
         * receives all the request attributes and the resolved controller arguments.
         *
         * The result must be a string that will be hashed.
         */
        public ?string $etag = null,

        /**
         * max-stale Cache-Control header
         * It can be expressed in seconds or with a relative time format (1 day, 2 weeks, ...).
         */
        public int|string|null $maxStale = null,

        /**
         * stale-while-revalidate Cache-Control header
         * It can be expressed in seconds or with a relative time format (1 day, 2 weeks, ...).
         */
        public int|string|null $staleWhileRevalidate = null,

        /**
         * stale-if-error Cache-Control header
         * It can be expressed in seconds or with a relative time format (1 day, 2 weeks, ...).
         */
        public int|string|null $staleIfError = null,

        /**
         * Add the "no-store" Cache-Control directive when set to true.
         *
         * This directive indicates that no part of the response can be cached
         * in any cache (not in a shared cache, nor in a private cache).
         *
         * Supersedes the "$public" and "$smaxage" values.
         *
         * @see https://datatracker.ietf.org/doc/html/rfc7234#section-5.2.2.3
         */
        public ?bool $noStore = null,
    ) {
    }
}
