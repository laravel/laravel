<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface SurrogateInterface
{
    /**
     * Returns surrogate name.
     */
    public function getName(): string;

    /**
     * Returns a new cache strategy instance.
     */
    public function createCacheStrategy(): ResponseCacheStrategyInterface;

    /**
     * Checks that at least one surrogate has Surrogate capability.
     */
    public function hasSurrogateCapability(Request $request): bool;

    /**
     * Adds Surrogate-capability to the given Request.
     */
    public function addSurrogateCapability(Request $request): void;

    /**
     * Adds HTTP headers to specify that the Response needs to be parsed for Surrogate.
     *
     * This method only adds an Surrogate HTTP header if the Response has some Surrogate tags.
     */
    public function addSurrogateControl(Response $response): void;

    /**
     * Checks that the Response needs to be parsed for Surrogate tags.
     */
    public function needsParsing(Response $response): bool;

    /**
     * Renders a Surrogate tag.
     *
     * @param string|null $alt     An alternate URI
     * @param string      $comment A comment to add as an esi:include tag
     */
    public function renderIncludeTag(string $uri, ?string $alt = null, bool $ignoreErrors = true, string $comment = ''): string;

    /**
     * Replaces a Response Surrogate tags with the included resource content.
     */
    public function process(Request $request, Response $response): Response;

    /**
     * Handles a Surrogate from the cache.
     *
     * @param string $alt An alternative URI
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function handle(HttpCache $cache, string $uri, string $alt, bool $ignoreErrors): string;
}
