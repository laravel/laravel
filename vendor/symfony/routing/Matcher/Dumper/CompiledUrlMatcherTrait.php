<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher\Dumper;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 *
 * @property RequestContext $context
 */
trait CompiledUrlMatcherTrait
{
    private bool $matchHost = false;
    private array $staticRoutes = [];
    private array $regexpList = [];
    private array $dynamicRoutes = [];
    private ?\Closure $checkCondition;

    public function match(string $pathinfo): array
    {
        $allow = $allowSchemes = [];
        if ($ret = $this->doMatch($pathinfo, $allow, $allowSchemes)) {
            return $ret;
        }
        if ($allow) {
            throw new MethodNotAllowedException(array_keys($allow));
        }
        if (!$this instanceof RedirectableUrlMatcherInterface) {
            throw new ResourceNotFoundException(\sprintf('No routes found for "%s".', $pathinfo));
        }
        if (!\in_array($this->context->getMethod(), ['HEAD', 'GET'], true)) {
            // no-op
        } elseif ($allowSchemes) {
            redirect_scheme:
            $scheme = $this->context->getScheme();
            $this->context->setScheme(key($allowSchemes));
            try {
                if ($ret = $this->doMatch($pathinfo)) {
                    return $this->redirect($pathinfo, $ret['_route'], $this->context->getScheme()) + $ret;
                }
            } finally {
                $this->context->setScheme($scheme);
            }
        } elseif ('' !== $trimmedPathinfo = rtrim($pathinfo, '/')) {
            $pathinfo = $trimmedPathinfo === $pathinfo ? $pathinfo.'/' : $trimmedPathinfo;
            if ($ret = $this->doMatch($pathinfo, $allow, $allowSchemes)) {
                return $this->redirect($pathinfo, $ret['_route']) + $ret;
            }
            if ($allowSchemes) {
                goto redirect_scheme;
            }
        }

        throw new ResourceNotFoundException(\sprintf('No routes found for "%s".', $pathinfo));
    }

    private function doMatch(string $pathinfo, array &$allow = [], array &$allowSchemes = []): array
    {
        $allow = $allowSchemes = [];
        $pathinfo = '' === ($pathinfo = rawurldecode($pathinfo)) ? '/' : $pathinfo;
        $trimmedPathinfo = '' === ($trimmedPathinfo = rtrim($pathinfo, '/')) ? '/' : $trimmedPathinfo;
        $context = $this->context;
        $requestMethod = $canonicalMethod = $context->getMethod();

        if ($this->matchHost) {
            $host = strtolower($context->getHost());
        }

        if ('HEAD' === $requestMethod) {
            $canonicalMethod = 'GET';
        }
        $supportsRedirections = 'GET' === $canonicalMethod && $this instanceof RedirectableUrlMatcherInterface;

        foreach ($this->staticRoutes[$trimmedPathinfo] ?? [] as [$ret, $requiredHost, $requiredMethods, $requiredSchemes, $hasTrailingSlash, , $condition]) {
            if ($requiredHost) {
                if ('{' !== $requiredHost[0] ? $requiredHost !== $host : !preg_match($requiredHost, $host, $hostMatches)) {
                    continue;
                }
                if ('{' === $requiredHost[0] && $hostMatches) {
                    $hostMatches['_route'] = $ret['_route'];
                    $ret = $this->mergeDefaults($hostMatches, $ret);
                }
            }

            if ($condition && !($this->checkCondition)($condition, $context, 0 < $condition ? $request ??= $this->request ?: $this->createRequest($pathinfo) : null, $ret)) {
                continue;
            }

            if ('/' !== $pathinfo && $hasTrailingSlash === ($trimmedPathinfo === $pathinfo)) {
                if ($supportsRedirections && (!$requiredMethods || isset($requiredMethods['GET']))) {
                    return $allow = $allowSchemes = [];
                }
                continue;
            }

            $hasRequiredScheme = !$requiredSchemes || isset($requiredSchemes[$context->getScheme()]);
            if ($hasRequiredScheme && $requiredMethods && !isset($requiredMethods[$canonicalMethod]) && !isset($requiredMethods[$requestMethod])) {
                $allow += $requiredMethods;
                continue;
            }

            if (!$hasRequiredScheme) {
                $allowSchemes += $requiredSchemes;
                continue;
            }

            return $ret;
        }

        $matchedPathinfo = $this->matchHost ? $host.'.'.$pathinfo : $pathinfo;

        foreach ($this->regexpList as $offset => $regex) {
            while (preg_match($regex, $matchedPathinfo, $matches)) {
                foreach ($this->dynamicRoutes[$m = (int) $matches['MARK']] as [$ret, $vars, $requiredMethods, $requiredSchemes, $hasTrailingSlash, $hasTrailingVar, $condition]) {
                    if (0 === $condition) { // marks the last route in the regexp
                        continue 3;
                    }

                    $hasTrailingVar = $trimmedPathinfo !== $pathinfo && $hasTrailingVar;

                    if ($hasTrailingVar && ($hasTrailingSlash || (null === $n = $matches[\count($vars)] ?? null) || '/' !== ($n[-1] ?? '/')) && preg_match($regex, $this->matchHost ? $host.'.'.$trimmedPathinfo : $trimmedPathinfo, $n) && $m === (int) $n['MARK']) {
                        if ($hasTrailingSlash) {
                            $matches = $n;
                        } else {
                            $hasTrailingVar = false;
                        }
                    }

                    foreach ($vars as $i => $v) {
                        if (isset($matches[1 + $i])) {
                            $ret[$v] = $matches[1 + $i];
                        }
                    }

                    if ($condition && !($this->checkCondition)($condition, $context, 0 < $condition ? $request ??= $this->request ?: $this->createRequest($pathinfo) : null, $ret)) {
                        continue;
                    }

                    if ('/' !== $pathinfo && !$hasTrailingVar && $hasTrailingSlash === ($trimmedPathinfo === $pathinfo)) {
                        if ($supportsRedirections && (!$requiredMethods || isset($requiredMethods['GET']))) {
                            return $allow = $allowSchemes = [];
                        }
                        continue;
                    }

                    if ($requiredSchemes && !isset($requiredSchemes[$context->getScheme()])) {
                        $allowSchemes += $requiredSchemes;
                        continue;
                    }

                    if ($requiredMethods && !isset($requiredMethods[$canonicalMethod]) && !isset($requiredMethods[$requestMethod])) {
                        $allow += $requiredMethods;
                        continue;
                    }

                    return $ret;
                }

                $regex = substr_replace($regex, 'F', $m - $offset, 1 + \strlen($m));
                $offset += \strlen($m);
            }
        }

        if ('/' === $pathinfo && !$allow && !$allowSchemes) {
            throw new NoConfigurationException();
        }

        return [];
    }
}
