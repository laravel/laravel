<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * TraceableUrlMatcher helps debug path info matching by tracing the match.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableUrlMatcher extends UrlMatcher
{
    public const ROUTE_DOES_NOT_MATCH = 0;
    public const ROUTE_ALMOST_MATCHES = 1;
    public const ROUTE_MATCHES = 2;

    protected array $traces;

    public function getTraces(string $pathinfo): array
    {
        $this->traces = [];

        try {
            $this->match($pathinfo);
        } catch (ExceptionInterface) {
        }

        return $this->traces;
    }

    public function getTracesForRequest(Request $request): array
    {
        $this->request = $request;
        $traces = $this->getTraces($request->getPathInfo());
        $this->request = null;

        return $traces;
    }

    protected function matchCollection(string $pathinfo, RouteCollection $routes): array
    {
        // HEAD and GET are equivalent as per RFC
        if ('HEAD' === $method = $this->context->getMethod()) {
            $method = 'GET';
        }
        $supportsTrailingSlash = 'GET' === $method && $this instanceof RedirectableUrlMatcherInterface;
        $trimmedPathinfo = '' === ($trimmedPathinfo = rtrim($pathinfo, '/')) ? '/' : $trimmedPathinfo;

        foreach ($routes as $name => $route) {
            $compiledRoute = $route->compile();
            $staticPrefix = rtrim($compiledRoute->getStaticPrefix(), '/');
            $requiredMethods = $route->getMethods();

            // check the static prefix of the URL first. Only use the more expensive preg_match when it matches
            if ('' !== $staticPrefix && !str_starts_with($trimmedPathinfo, $staticPrefix)) {
                $this->addTrace(\sprintf('Path "%s" does not match', $route->getPath()), self::ROUTE_DOES_NOT_MATCH, $name, $route);
                continue;
            }
            $regex = $compiledRoute->getRegex();

            $pos = strrpos($regex, '$');
            $hasTrailingSlash = '/' === $regex[$pos - 1];
            $regex = substr_replace($regex, '/?$', $pos - $hasTrailingSlash, 1 + $hasTrailingSlash);

            if (!preg_match($regex, $pathinfo, $matches)) {
                // does it match without any requirements?
                $r = new Route($route->getPath(), $route->getDefaults(), [], $route->getOptions());
                $cr = $r->compile();
                if (!preg_match($cr->getRegex(), $pathinfo)) {
                    $this->addTrace(\sprintf('Path "%s" does not match', $route->getPath()), self::ROUTE_DOES_NOT_MATCH, $name, $route);

                    continue;
                }

                foreach ($route->getRequirements() as $n => $regex) {
                    $r = new Route($route->getPath(), $route->getDefaults(), [$n => $regex], $route->getOptions());
                    $cr = $r->compile();

                    if (\in_array($n, $cr->getVariables()) && !preg_match($cr->getRegex(), $pathinfo)) {
                        $this->addTrace(\sprintf('Requirement for "%s" does not match (%s)', $n, $regex), self::ROUTE_ALMOST_MATCHES, $name, $route);

                        continue 2;
                    }
                }

                continue;
            }

            $hasTrailingVar = $trimmedPathinfo !== $pathinfo && preg_match('#\{[\w\x80-\xFF]+\}/?$#', $route->getPath());

            if ($hasTrailingVar && ($hasTrailingSlash || (null === $m = $matches[\count($compiledRoute->getPathVariables())] ?? null) || '/' !== ($m[-1] ?? '/')) && preg_match($regex, $trimmedPathinfo, $m)) {
                if ($hasTrailingSlash) {
                    $matches = $m;
                } else {
                    $hasTrailingVar = false;
                }
            }

            $hostMatches = [];
            if ($compiledRoute->getHostRegex() && !preg_match($compiledRoute->getHostRegex(), $this->context->getHost(), $hostMatches)) {
                $this->addTrace(\sprintf('Host "%s" does not match the requirement ("%s")', $this->context->getHost(), $route->getHost()), self::ROUTE_ALMOST_MATCHES, $name, $route);
                continue;
            }

            $attributes = $this->getAttributes($route, $name, array_replace($matches, $hostMatches));

            $status = $this->handleRouteRequirements($pathinfo, $name, $route, $attributes);

            if (self::REQUIREMENT_MISMATCH === $status[0]) {
                $this->addTrace(\sprintf('Condition "%s" does not evaluate to "true"', $route->getCondition()), self::ROUTE_ALMOST_MATCHES, $name, $route);
                continue;
            }

            if ('/' !== $pathinfo && !$hasTrailingVar && $hasTrailingSlash === ($trimmedPathinfo === $pathinfo)) {
                if ($supportsTrailingSlash && (!$requiredMethods || \in_array('GET', $requiredMethods, true))) {
                    $this->addTrace('Route matches!', self::ROUTE_MATCHES, $name, $route);

                    return $this->allow = $this->allowSchemes = [];
                }
                $this->addTrace(\sprintf('Path "%s" does not match', $route->getPath()), self::ROUTE_DOES_NOT_MATCH, $name, $route);
                continue;
            }

            if ($route->getSchemes() && !$route->hasScheme($this->context->getScheme())) {
                $this->allowSchemes = array_merge($this->allowSchemes, $route->getSchemes());
                $this->addTrace(\sprintf('Scheme "%s" does not match any of the required schemes (%s)', $this->context->getScheme(), implode(', ', $route->getSchemes())), self::ROUTE_ALMOST_MATCHES, $name, $route);
                continue;
            }

            if ($requiredMethods && !\in_array($method, $requiredMethods, true)) {
                $this->allow = array_merge($this->allow, $requiredMethods);
                $this->addTrace(\sprintf('Method "%s" does not match any of the required methods (%s)', $this->context->getMethod(), implode(', ', $requiredMethods)), self::ROUTE_ALMOST_MATCHES, $name, $route);
                continue;
            }

            $this->addTrace('Route matches!', self::ROUTE_MATCHES, $name, $route);

            return array_replace($attributes, $status[1] ?? []);
        }

        return [];
    }

    private function addTrace(string $log, int $level = self::ROUTE_DOES_NOT_MATCH, ?string $name = null, ?Route $route = null): void
    {
        $this->traces[] = [
            'log' => $log,
            'name' => $name,
            'level' => $level,
            'path' => $route?->getPath(),
        ];
    }
}
