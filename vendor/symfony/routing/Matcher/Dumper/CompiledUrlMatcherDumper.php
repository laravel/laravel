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

use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * CompiledUrlMatcherDumper creates PHP arrays to be used with CompiledUrlMatcher.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CompiledUrlMatcherDumper extends MatcherDumper
{
    private ExpressionLanguage $expressionLanguage;
    private ?\Exception $signalingException = null;

    /**
     * @var ExpressionFunctionProviderInterface[]
     */
    private array $expressionLanguageProviders = [];

    public function dump(array $options = []): string
    {
        return <<<EOF
            <?php

            /**
             * This file has been auto-generated
             * by the Symfony Routing Component.
             */

            return [
            {$this->generateCompiledRoutes()}];

            EOF;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider): void
    {
        $this->expressionLanguageProviders[] = $provider;
    }

    /**
     * Generates the arrays for CompiledUrlMatcher's constructor.
     */
    public function getCompiledRoutes(bool $forDump = false): array
    {
        // Group hosts by same-suffix, re-order when possible
        $matchHost = false;
        $routes = new StaticPrefixCollection();
        foreach ($this->getRoutes()->all() as $name => $route) {
            if ($host = $route->getHost()) {
                $matchHost = true;
                $host = '/'.strtr(strrev($host), '}.{', '(/)');
            }

            $routes->addRoute($host ?: '/(.*)', [$name, $route]);
        }

        if ($matchHost) {
            $compiledRoutes = [true];
            $routes = $routes->populateCollection(new RouteCollection());
        } else {
            $compiledRoutes = [false];
            $routes = $this->getRoutes();
        }

        [$staticRoutes, $dynamicRoutes] = $this->groupStaticRoutes($routes);

        $conditions = [null];
        $compiledRoutes[] = $this->compileStaticRoutes($staticRoutes, $conditions);
        $chunkLimit = \count($dynamicRoutes);

        while (true) {
            try {
                $this->signalingException = new \RuntimeException('Compilation failed: regular expression is too large');
                $compiledRoutes = array_merge($compiledRoutes, $this->compileDynamicRoutes($dynamicRoutes, $matchHost, $chunkLimit, $conditions));

                break;
            } catch (\Exception $e) {
                if (1 < $chunkLimit && $this->signalingException === $e) {
                    $chunkLimit = 1 + ($chunkLimit >> 1);
                    continue;
                }
                throw $e;
            }
        }

        if ($forDump) {
            $compiledRoutes[2] = $compiledRoutes[4];
        }
        unset($conditions[0]);

        if ($conditions) {
            foreach ($conditions as $expression => $condition) {
                $conditions[$expression] = "case {$condition}: return {$expression};";
            }

            $checkConditionCode = <<<EOF
                    static function (\$condition, \$context, \$request, \$params) { // \$checkCondition
                        switch (\$condition) {
                {$this->indent(implode("\n", $conditions), 3)}
                        }
                    }
                EOF;
            $compiledRoutes[4] = $forDump ? $checkConditionCode.",\n" : eval('return '.$checkConditionCode.';');
        } else {
            $compiledRoutes[4] = $forDump ? "    null, // \$checkCondition\n" : null;
        }

        return $compiledRoutes;
    }

    private function generateCompiledRoutes(): string
    {
        [$matchHost, $staticRoutes, $regexpCode, $dynamicRoutes, $checkConditionCode] = $this->getCompiledRoutes(true);

        $code = self::export($matchHost).', // $matchHost'."\n";

        $code .= '[ // $staticRoutes'."\n";
        foreach ($staticRoutes as $path => $routes) {
            $code .= \sprintf("    %s => [\n", self::export($path));
            foreach ($routes as $route) {
                $code .= vsprintf("        [%s, %s, %s, %s, %s, %s, %s],\n", array_map([__CLASS__, 'export'], $route));
            }
            $code .= "    ],\n";
        }
        $code .= "],\n";

        $code .= \sprintf("[ // \$regexpList%s\n],\n", $regexpCode);

        $code .= '[ // $dynamicRoutes'."\n";
        foreach ($dynamicRoutes as $path => $routes) {
            $code .= \sprintf("    %s => [\n", self::export($path));
            foreach ($routes as $route) {
                $code .= vsprintf("        [%s, %s, %s, %s, %s, %s, %s],\n", array_map([__CLASS__, 'export'], $route));
            }
            $code .= "    ],\n";
        }
        $code .= "],\n";
        $code = preg_replace('/ => \[\n        (\[.+?),\n    \],/', ' => [$1],', $code);

        return $this->indent($code, 1).$checkConditionCode;
    }

    /**
     * Splits static routes from dynamic routes, so that they can be matched first, using a simple switch.
     */
    private function groupStaticRoutes(RouteCollection $collection): array
    {
        $staticRoutes = $dynamicRegex = [];
        $dynamicRoutes = new RouteCollection();

        foreach ($collection->all() as $name => $route) {
            $compiledRoute = $route->compile();
            $staticPrefix = rtrim($compiledRoute->getStaticPrefix(), '/');
            $hostRegex = $compiledRoute->getHostRegex();
            $regex = $compiledRoute->getRegex();
            if ($hasTrailingSlash = '/' !== $route->getPath()) {
                $pos = strrpos($regex, '$');
                $hasTrailingSlash = '/' === $regex[$pos - 1];
                $regex = substr_replace($regex, '/?$', $pos - $hasTrailingSlash, 1 + $hasTrailingSlash);
            }

            if (!$compiledRoute->getPathVariables()) {
                $host = !$compiledRoute->getHostVariables() ? $route->getHost() : '';
                $url = $route->getPath();
                if ($hasTrailingSlash) {
                    $url = substr($url, 0, -1);
                }
                foreach ($dynamicRegex as [$hostRx, $rx, $prefix]) {
                    if (('' === $prefix || str_starts_with($url, $prefix)) && (preg_match($rx, $url) || preg_match($rx, $url.'/')) && (!$host || !$hostRx || preg_match($hostRx, $host))) {
                        $dynamicRegex[] = [$hostRegex, $regex, $staticPrefix];
                        $dynamicRoutes->add($name, $route);
                        continue 2;
                    }
                }

                $staticRoutes[$url][$name] = [$route, $hasTrailingSlash];
            } else {
                $dynamicRegex[] = [$hostRegex, $regex, $staticPrefix];
                $dynamicRoutes->add($name, $route);
            }
        }

        return [$staticRoutes, $dynamicRoutes];
    }

    /**
     * Compiles static routes in a switch statement.
     *
     * Condition-less paths are put in a static array in the switch's default, with generic matching logic.
     * Paths that can match two or more routes, or have user-specified conditions are put in separate switch's cases.
     *
     * @throws \LogicException
     */
    private function compileStaticRoutes(array $staticRoutes, array &$conditions): array
    {
        if (!$staticRoutes) {
            return [];
        }
        $compiledRoutes = [];

        foreach ($staticRoutes as $url => $routes) {
            $compiledRoutes[$url] = [];
            foreach ($routes as $name => [$route, $hasTrailingSlash]) {
                if ($route->compile()->getHostVariables()) {
                    $host = $route->compile()->getHostRegex();
                } elseif ($host = $route->getHost()) {
                    $host = strtolower($host);
                }
                $compiledRoutes[$url][] = $this->compileRoute($route, $name, $host ?: null, $hasTrailingSlash, false, $conditions);
            }
        }

        return $compiledRoutes;
    }

    /**
     * Compiles a regular expression followed by a switch statement to match dynamic routes.
     *
     * The regular expression matches both the host and the pathinfo at the same time. For stellar performance,
     * it is built as a tree of patterns, with re-ordering logic to group same-prefix routes together when possible.
     *
     * Patterns are named so that we know which one matched (https://pcre.org/current/doc/html/pcre2syntax.html#SEC23).
     * This name is used to "switch" to the additional logic required to match the final route.
     *
     * Condition-less paths are put in a static array in the switch's default, with generic matching logic.
     * Paths that can match two or more routes, or have user-specified conditions are put in separate switch's cases.
     *
     * Last but not least:
     *  - Because it is not possible to mix unicode/non-unicode patterns in a single regexp, several of them can be generated.
     *  - The same regexp can be used several times when the logic in the switch rejects the match. When this happens, the
     *    matching-but-failing subpattern is excluded by replacing its name by "(*F)", which forces a failure-to-match.
     *    To ease this backlisting operation, the name of subpatterns is also the string offset where the replacement should occur.
     */
    private function compileDynamicRoutes(RouteCollection $collection, bool $matchHost, int $chunkLimit, array &$conditions): array
    {
        if (!$collection->all()) {
            return [[], [], ''];
        }
        $regexpList = [];
        $code = '';
        $state = (object) [
            'regexMark' => 0,
            'regex' => [],
            'routes' => [],
            'mark' => 0,
            'markTail' => 0,
            'hostVars' => [],
            'vars' => [],
        ];
        $state->getVars = static function ($m) use ($state) {
            if ('_route' === $m[1]) {
                return '?:';
            }

            $state->vars[] = $m[1];

            return '';
        };

        $chunkSize = 0;
        $prev = null;
        $perModifiers = [];
        foreach ($collection->all() as $name => $route) {
            preg_match('#[a-zA-Z]*$#', $route->compile()->getRegex(), $rx);
            if ($chunkLimit < ++$chunkSize || $prev !== $rx[0] && $route->compile()->getPathVariables()) {
                $chunkSize = 1;
                $routes = new RouteCollection();
                $perModifiers[] = [$rx[0], $routes];
                $prev = $rx[0];
            }
            $routes->add($name, $route);
        }

        foreach ($perModifiers as [$modifiers, $routes]) {
            $prev = false;
            $perHost = [];
            foreach ($routes->all() as $name => $route) {
                $regex = $route->compile()->getHostRegex();
                if ($prev !== $regex) {
                    $routes = new RouteCollection();
                    $perHost[] = [$regex, $routes];
                    $prev = $regex;
                }
                $routes->add($name, $route);
            }
            $prev = false;
            $rx = '{^(?';
            $code .= "\n    {$state->mark} => ".self::export($rx);
            $startingMark = $state->mark;
            $state->mark += \strlen($rx);
            $state->regex = $rx;

            foreach ($perHost as [$hostRegex, $routes]) {
                if ($matchHost) {
                    if ($hostRegex) {
                        preg_match('#^.\^(.*)\$.[a-zA-Z]*$#', $hostRegex, $rx);
                        $state->vars = [];
                        $hostRegex = '(?i:'.preg_replace_callback('#\?P<([^>]++)>#', $state->getVars, $rx[1]).')\.';
                        $state->hostVars = $state->vars;
                    } else {
                        $hostRegex = '(?:(?:[^./]*+\.)++)';
                        $state->hostVars = [];
                    }
                    $state->mark += \strlen($rx = ($prev ? ')' : '')."|{$hostRegex}(?");
                    $code .= "\n        .".self::export($rx);
                    $state->regex .= $rx;
                    $prev = true;
                }

                $tree = new StaticPrefixCollection();
                foreach ($routes->all() as $name => $route) {
                    preg_match('#^.\^(.*)\$.[a-zA-Z]*$#', $route->compile()->getRegex(), $rx);

                    $state->vars = [];
                    $regex = preg_replace_callback('#\?P<([^>]++)>#', $state->getVars, $rx[1]);
                    if ($hasTrailingSlash = '/' !== $regex && '/' === $regex[-1]) {
                        $regex = substr($regex, 0, -1);
                    }
                    $hasTrailingVar = (bool) preg_match('#\{[\w\x80-\xFF]+\}/?$#', $route->getPath());

                    $tree->addRoute($regex, [$name, $regex, $state->vars, $route, $hasTrailingSlash, $hasTrailingVar]);
                }

                $code .= $this->compileStaticPrefixCollection($tree, $state, 0, $conditions);
            }
            if ($matchHost) {
                $code .= "\n        .')'";
                $state->regex .= ')';
            }
            $rx = ")/?$}{$modifiers}";
            $code .= "\n        .'{$rx}',";
            $state->regex .= $rx;
            $state->markTail = 0;

            // if the regex is too large, throw a signaling exception to recompute with smaller chunk size
            set_error_handler(fn ($type, $message) => throw str_contains($message, $this->signalingException->getMessage()) ? $this->signalingException : new \ErrorException($message));
            try {
                preg_match($state->regex, '');
            } finally {
                restore_error_handler();
            }

            $regexpList[$startingMark] = $state->regex;
        }

        $state->routes[$state->mark][] = [null, null, null, null, false, false, 0];
        unset($state->getVars);

        return [$regexpList, $state->routes, $code];
    }

    /**
     * Compiles a regexp tree of subpatterns that matches nested same-prefix routes.
     *
     * @param \stdClass $state A simple state object that keeps track of the progress of the compilation,
     *                         and gathers the generated switch's "case" and "default" statements
     */
    private function compileStaticPrefixCollection(StaticPrefixCollection $tree, \stdClass $state, int $prefixLen, array &$conditions): string
    {
        $code = '';
        $prevRegex = null;
        $routes = $tree->getRoutes();

        foreach ($routes as $i => $route) {
            if ($route instanceof StaticPrefixCollection) {
                $prevRegex = null;
                $prefix = substr($route->getPrefix(), $prefixLen);
                $state->mark += \strlen($rx = "|{$prefix}(?");
                $code .= "\n            .".self::export($rx);
                $state->regex .= $rx;
                $code .= $this->indent($this->compileStaticPrefixCollection($route, $state, $prefixLen + \strlen($prefix), $conditions));
                $code .= "\n            .')'";
                $state->regex .= ')';
                ++$state->markTail;
                continue;
            }

            [$name, $regex, $vars, $route, $hasTrailingSlash, $hasTrailingVar] = $route;
            $compiledRoute = $route->compile();
            $vars = array_merge($state->hostVars, $vars);

            if ($compiledRoute->getRegex() === $prevRegex) {
                $state->routes[$state->mark][] = $this->compileRoute($route, $name, $vars, $hasTrailingSlash, $hasTrailingVar, $conditions);
                continue;
            }

            $state->mark += 3 + $state->markTail + \strlen($regex) - $prefixLen;
            $state->markTail = 2 + \strlen($state->mark);
            $rx = \sprintf('|%s(*:%s)', substr($regex, $prefixLen), $state->mark);
            $code .= "\n            .".self::export($rx);
            $state->regex .= $rx;

            $prevRegex = $compiledRoute->getRegex();
            $state->routes[$state->mark] = [$this->compileRoute($route, $name, $vars, $hasTrailingSlash, $hasTrailingVar, $conditions)];
        }

        return $code;
    }

    /**
     * Compiles a single Route to PHP code used to match it against the path info.
     */
    private function compileRoute(Route $route, string $name, string|array|null $vars, bool $hasTrailingSlash, bool $hasTrailingVar, array &$conditions): array
    {
        $defaults = $route->getDefaults();

        if (isset($defaults['_canonical_route'])) {
            $name = $defaults['_canonical_route'];
            unset($defaults['_canonical_route']);
        }

        if ($condition = $route->getCondition()) {
            $condition = $this->getExpressionLanguage()->compile($condition, ['context', 'request', 'params']);
            $condition = $conditions[$condition] ??= (str_contains($condition, '$request') ? 1 : -1) * \count($conditions);
        } else {
            $condition = null;
        }

        return [
            ['_route' => $name] + $defaults,
            $vars,
            array_flip($route->getMethods()) ?: null,
            array_flip($route->getSchemes()) ?: null,
            $hasTrailingSlash,
            $hasTrailingVar,
            $condition,
        ];
    }

    private function getExpressionLanguage(): ExpressionLanguage
    {
        if (!isset($this->expressionLanguage)) {
            if (!class_exists(ExpressionLanguage::class)) {
                throw new \LogicException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed. Try running "composer require symfony/expression-language".');
            }
            $this->expressionLanguage = new ExpressionLanguage(null, $this->expressionLanguageProviders);
        }

        return $this->expressionLanguage;
    }

    private function indent(string $code, int $level = 1): string
    {
        return preg_replace('/^./m', str_repeat('    ', $level).'$0', $code);
    }

    /**
     * @internal
     */
    public static function export(mixed $value): string
    {
        if (null === $value) {
            return 'null';
        }
        if (\is_object($value)) {
            throw new \InvalidArgumentException(\sprintf('Symfony\Component\Routing\Route cannot contain objects, but "%s" given.', get_debug_type($value)));
        }
        if (!\is_array($value)) {
            return str_replace("\n", '\'."\n".\'', var_export($value, true));
        }
        if (!$value) {
            return '[]';
        }

        $i = 0;
        $export = '[';

        foreach ($value as $k => $v) {
            if ($i === $k) {
                ++$i;
            } else {
                $export .= self::export($k).' => ';

                if (\is_int($k) && $i < $k) {
                    $i = 1 + $k;
                }
            }

            $export .= self::export($v).', ';
        }

        return substr_replace($export, ']', -2);
    }
}
