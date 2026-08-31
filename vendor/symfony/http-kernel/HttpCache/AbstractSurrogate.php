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
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Abstract class implementing Surrogate capabilities to Request and Response instances.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
abstract class AbstractSurrogate implements SurrogateInterface
{
    /**
     * @param array $contentTypes An array of content-type that should be parsed for Surrogate information
     *                            (default: text/html, text/xml, application/xhtml+xml, and application/xml)
     */
    public function __construct(
        protected array $contentTypes = ['text/html', 'text/xml', 'application/xhtml+xml', 'application/xml'],
    ) {
    }

    /**
     * Returns a new cache strategy instance.
     */
    public function createCacheStrategy(): ResponseCacheStrategyInterface
    {
        return new ResponseCacheStrategy();
    }

    public function hasSurrogateCapability(Request $request): bool
    {
        if (null === $value = $request->headers->get('Surrogate-Capability')) {
            return false;
        }

        return str_contains($value, \sprintf('%s/1.0', strtoupper($this->getName())));
    }

    public function addSurrogateCapability(Request $request): void
    {
        $current = $request->headers->get('Surrogate-Capability');
        $new = \sprintf('symfony="%s/1.0"', strtoupper($this->getName()));

        $request->headers->set('Surrogate-Capability', $current ? $current.', '.$new : $new);
    }

    public function needsParsing(Response $response): bool
    {
        if (!$control = $response->headers->get('Surrogate-Control')) {
            return false;
        }

        $pattern = \sprintf('#content="[^"]*%s/1.0[^"]*"#', strtoupper($this->getName()));

        return (bool) preg_match($pattern, $control);
    }

    public function handle(HttpCache $cache, string $uri, string $alt, bool $ignoreErrors): string
    {
        $subRequest = Request::create($uri, 'GET', [], $cache->getRequest()->cookies->all(), [], $cache->getRequest()->server->all());

        try {
            $response = $cache->handle($subRequest, HttpKernelInterface::SUB_REQUEST, true);

            if (!$response->isSuccessful() && Response::HTTP_NOT_MODIFIED !== $response->getStatusCode()) {
                throw new \RuntimeException(\sprintf('Error when rendering "%s" (Status code is %d).', $subRequest->getUri(), $response->getStatusCode()));
            }

            return $response->getContent();
        } catch (\Exception $e) {
            if ($alt) {
                return $this->handle($cache, $alt, '', $ignoreErrors);
            }

            if (!$ignoreErrors) {
                throw $e;
            }
        }

        return '';
    }

    /**
     * Remove the Surrogate from the Surrogate-Control header.
     */
    protected function removeFromControl(Response $response): void
    {
        if (!$response->headers->has('Surrogate-Control')) {
            return;
        }

        $value = $response->headers->get('Surrogate-Control');
        $upperName = strtoupper($this->getName());

        if (\sprintf('content="%s/1.0"', $upperName) == $value) {
            $response->headers->remove('Surrogate-Control');
        } elseif (preg_match(\sprintf('#,\s*content="%s/1.0"#', $upperName), $value)) {
            $response->headers->set('Surrogate-Control', preg_replace(\sprintf('#,\s*content="%s/1.0"#', $upperName), '', $value));
        } elseif (preg_match(\sprintf('#content="%s/1.0",\s*#', $upperName), $value)) {
            $response->headers->set('Surrogate-Control', preg_replace(\sprintf('#content="%s/1.0",\s*#', $upperName), '', $value));
        }
    }

    protected static function generateBodyEvalBoundary(): string
    {
        static $cookie;
        $cookie = hash('xxh128', $cookie ?? $cookie = random_bytes(16), true);
        $boundary = base64_encode($cookie);

        \assert(HttpCache::BODY_EVAL_BOUNDARY_LENGTH === \strlen($boundary));

        return $boundary;
    }
}
