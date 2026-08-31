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

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RequestContextAwareInterface;

/**
 * A helper service for manipulating URLs within and outside the request scope.
 *
 * @author Valentin Udaltsov <udaltsov.valentin@gmail.com>
 */
final class UrlHelper
{
    public function __construct(
        private RequestStack $requestStack,
        private RequestContextAwareInterface|RequestContext|null $requestContext = null,
    ) {
    }

    public function getAbsoluteUrl(string $path): string
    {
        if (str_contains($path, '://') || str_starts_with($path, '//')) {
            return $path;
        }

        if (null === $request = $this->requestStack->getMainRequest()) {
            return $this->getAbsoluteUrlFromContext($path);
        }

        if ('#' === $path[0]) {
            $path = $request->getRequestUri().$path;
        } elseif ('?' === $path[0]) {
            $path = $request->getPathInfo().$path;
        }

        if (!$path || '/' !== $path[0]) {
            $prefix = $request->getPathInfo();
            $last = \strlen($prefix) - 1;
            if ($last !== $pos = strrpos($prefix, '/')) {
                $prefix = substr($prefix, 0, $pos).'/';
            }

            return $request->getUriForPath($prefix.$path);
        }

        return $request->getSchemeAndHttpHost().$path;
    }

    public function getRelativePath(string $path): string
    {
        if (str_contains($path, '://') || str_starts_with($path, '//')) {
            return $path;
        }

        if (null === $request = $this->requestStack->getMainRequest()) {
            return $path;
        }

        return $request->getRelativeUriForPath($path);
    }

    private function getAbsoluteUrlFromContext(string $path): string
    {
        if (null === $context = $this->requestContext) {
            return $path;
        }

        if ($context instanceof RequestContextAwareInterface) {
            $context = $context->getContext();
        }

        if ('' === $host = $context->getHost()) {
            return $path;
        }

        $scheme = $context->getScheme();
        $port = '';

        if ('http' === $scheme && 80 !== $context->getHttpPort()) {
            $port = ':'.$context->getHttpPort();
        } elseif ('https' === $scheme && 443 !== $context->getHttpsPort()) {
            $port = ':'.$context->getHttpsPort();
        }

        if ('#' === $path[0]) {
            $queryString = $context->getQueryString();
            $path = $context->getPathInfo().($queryString ? '?'.$queryString : '').$path;
        } elseif ('?' === $path[0]) {
            $path = $context->getPathInfo().$path;
        }

        if ('/' !== $path[0]) {
            $path = rtrim($context->getBaseUrl(), '/').'/'.$path;
        }

        return $scheme.'://'.$host.$port.$path;
    }
}
