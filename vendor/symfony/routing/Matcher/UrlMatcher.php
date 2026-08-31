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

use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * UrlMatcher matches URL based on a set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UrlMatcher implements UrlMatcherInterface, RequestMatcherInterface
{
    public const REQUIREMENT_MATCH = 0;
    public const REQUIREMENT_MISMATCH = 1;
    public const ROUTE_MATCH = 2;

    /**
     * Collects HTTP methods that would be allowed for the request.
     */
    protected array $allow = [];

    /**
     * Collects URI schemes that would be allowed for the request.
     *
     * @internal
     */
    protected array $allowSchemes = [];
    protected ?Request $request = null;
    protected ExpressionLanguage $expressionLanguage;

    /**
     * @var ExpressionFunctionProviderInterface[]
     */
    protected array $expressionLanguageProviders = [];

    public function __construct(
        protected RouteCollection $routes,
        protected RequestContext $context,
    ) {
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function match(string $pathinfo): array
    {
        $this->allow = $this->allowSchemes = [];
        $pathinfo = '' === ($pathinfo = rawurldecode($pathinfo)) ? '/' : $pathinfo;

        if ($ret = $this->matchCollection($pathinfo, $this->routes)) {
            return $ret;
        }

        if ('/' === $pathinfo && !$this->allow && !$this->allowSchemes) {
            throw new NoConfigurationException();
        }

        throw 0 < \count($this->allow) ? new MethodNotAllowedException(array_unique($this->allow)) : new ResourceNotFoundException(\sprintf('No routes found for "%s".', $pathinfo));
    }

    public function matchRequest(Request $request): array
    {
        $this->request = $request;
        $originalContext = $this->context;
        $this->context = (clone $originalContext)->fromRequest($request);

        try {
            return $this->match($request->getPathInfo());
        } finally {
            $this->context = $originalContext;
            $this->request = null;
        }
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider): void
    {
        $this->expressionLanguageProviders[] = $provider;
    }

    /**
     * Tries to match a URL with a set of routes.
     *
     * @param string $pathinfo The path info to be parsed
     *
     * @throws NoConfigurationException  If no routing configuration could be found
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     */
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
                continue;
            }
            $regex = $compiledRoute->getRegex();

            $pos = strrpos($regex, '$');
            $hasTrailingSlash = '/' === $regex[$pos - 1];
            $regex = substr_replace($regex, '/?$', $pos - $hasTrailingSlash, 1 + $hasTrailingSlash);

            if (!preg_match($regex, $pathinfo, $matches)) {
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
                continue;
            }

            $attributes = $this->getAttributes($route, $name, array_replace($matches, $hostMatches));

            $status = $this->handleRouteRequirements($pathinfo, $name, $route, $attributes);

            if (self::REQUIREMENT_MISMATCH === $status[0]) {
                continue;
            }

            if ('/' !== $pathinfo && !$hasTrailingVar && $hasTrailingSlash === ($trimmedPathinfo === $pathinfo)) {
                if ($supportsTrailingSlash && (!$requiredMethods || \in_array('GET', $requiredMethods, true))) {
                    return $this->allow = $this->allowSchemes = [];
                }
                continue;
            }

            if ($route->getSchemes() && !$route->hasScheme($this->context->getScheme())) {
                $this->allowSchemes = array_merge($this->allowSchemes, $route->getSchemes());
                continue;
            }

            if ($requiredMethods && !\in_array($method, $requiredMethods, true)) {
                $this->allow = array_merge($this->allow, $requiredMethods);
                continue;
            }

            return array_replace($attributes, $status[1] ?? []);
        }

        return [];
    }

    /**
     * Returns an array of values to use as request attributes.
     *
     * As this method requires the Route object, it is not available
     * in matchers that do not have access to the matched Route instance
     * (like the PHP and Apache matcher dumpers).
     */
    protected function getAttributes(Route $route, string $name, array $attributes): array
    {
        $defaults = $route->getDefaults();
        if (isset($defaults['_canonical_route'])) {
            $name = $defaults['_canonical_route'];
            unset($defaults['_canonical_route']);
        }
        $attributes['_route'] = $name;

        if ($mapping = $route->getOption('mapping')) {
            $attributes['_route_mapping'] = $mapping;
        }

        return $this->mergeDefaults($attributes, $defaults);
    }

    /**
     * Handles specific route requirements.
     *
     * @return array The first element represents the status, the second contains additional information
     */
    protected function handleRouteRequirements(string $pathinfo, string $name, Route $route, array $routeParameters): array
    {
        // expression condition
        if ($route->getCondition() && !$this->getExpressionLanguage()->evaluate($route->getCondition(), [
            'context' => $this->context,
            'request' => $this->request ?: $this->createRequest($pathinfo),
            'params' => $routeParameters,
        ])) {
            return [self::REQUIREMENT_MISMATCH, null];
        }

        return [self::REQUIREMENT_MATCH, null];
    }

    /**
     * Get merged default parameters.
     */
    protected function mergeDefaults(array $params, array $defaults): array
    {
        foreach ($params as $key => $value) {
            if (!\is_int($key) && null !== $value) {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    protected function getExpressionLanguage(): ExpressionLanguage
    {
        if (!isset($this->expressionLanguage)) {
            if (!class_exists(ExpressionLanguage::class)) {
                throw new \LogicException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed. Try running "composer require symfony/expression-language".');
            }
            $this->expressionLanguage = new ExpressionLanguage(null, $this->expressionLanguageProviders);
        }

        return $this->expressionLanguage;
    }

    /**
     * @internal
     */
    protected function createRequest(string $pathinfo): ?Request
    {
        if (!class_exists(Request::class)) {
            return null;
        }

        return Request::create($this->context->getScheme().'://'.$this->context->getHost().$this->context->getBaseUrl().$pathinfo, $this->context->getMethod(), $this->context->getParameters(), [], [], [
            'SCRIPT_FILENAME' => $this->context->getBaseUrl(),
            'SCRIPT_NAME' => $this->context->getBaseUrl(),
        ]);
    }
}
