<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing;

/**
 * A Route describes a route and its parameters.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class Route
{
    private string $path = '/';
    private string $host = '';
    private array $schemes = [];
    private array $methods = [];
    private array $defaults = [];
    private array $requirements = [];
    private array $options = [];
    private string $condition = '';
    private ?CompiledRoute $compiled = null;

    /**
     * Constructor.
     *
     * Available options:
     *
     *  * compiler_class: A class name able to compile this route instance (RouteCompiler by default)
     *  * utf8:           Whether UTF-8 matching is enforced or not
     *
     * @param string                    $path         The path pattern to match
     * @param array                     $defaults     An array of default parameter values
     * @param array<string|\Stringable> $requirements An array of requirements for parameters (regexes)
     * @param array                     $options      An array of options
     * @param string|null               $host         The host pattern to match
     * @param string|string[]           $schemes      A required URI scheme or an array of restricted schemes
     * @param string|string[]           $methods      A required HTTP method or an array of restricted methods
     * @param string|null               $condition    A condition that should evaluate to true for the route to match
     */
    public function __construct(string $path, array $defaults = [], array $requirements = [], array $options = [], ?string $host = '', string|array $schemes = [], string|array $methods = [], ?string $condition = '')
    {
        $this->setPath($path);
        $this->addDefaults($defaults);
        $this->addRequirements($requirements);
        $this->setOptions($options);
        $this->setHost($host);
        $this->setSchemes($schemes);
        $this->setMethods($methods);
        $this->setCondition($condition);
    }

    public function __serialize(): array
    {
        return [
            'path' => $this->path,
            'host' => $this->host,
            'defaults' => $this->defaults,
            'requirements' => $this->requirements,
            'options' => $this->options,
            'schemes' => $this->schemes,
            'methods' => $this->methods,
            'condition' => $this->condition,
            'compiled' => $this->compiled,
        ];
    }

    public function __unserialize(array $data): void
    {
        if (($data['path'] ?? null) instanceof \Stringable
            || ($data['host'] ?? null) instanceof \Stringable
            || ($data['condition'] ?? null) instanceof \Stringable
        ) {
            throw new \BadMethodCallException('Cannot unserialize '.self::class);
        }

        $this->path = $data['path'];
        $this->host = $data['host'];
        $this->defaults = $data['defaults'];
        $this->requirements = $data['requirements'];
        $this->options = $data['options'];
        $this->schemes = $data['schemes'];
        $this->methods = $data['methods'];

        if (isset($data['condition'])) {
            $this->condition = $data['condition'];
        }
        if (isset($data['compiled'])) {
            $this->compiled = $data['compiled'];
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return $this
     */
    public function setPath(string $pattern): static
    {
        $pattern = $this->extractInlineDefaultsAndRequirements($pattern);

        // A pattern must start with a slash and must not have multiple slashes at the beginning because the
        // generated path for this route would be confused with a network path, e.g. '//domain.com/path'.
        $this->path = '/'.ltrim(trim($pattern), '/');
        $this->compiled = null;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return $this
     */
    public function setHost(?string $pattern): static
    {
        $this->host = $this->extractInlineDefaultsAndRequirements((string) $pattern);
        $this->compiled = null;

        return $this;
    }

    /**
     * Returns the lowercased schemes this route is restricted to.
     * So an empty array means that any scheme is allowed.
     *
     * @return string[]
     */
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    /**
     * Sets the schemes (e.g. 'https') this route is restricted to.
     * So an empty array means that any scheme is allowed.
     *
     * @param string|string[] $schemes The scheme or an array of schemes
     *
     * @return $this
     */
    public function setSchemes(string|array $schemes): static
    {
        $this->schemes = array_map('strtolower', (array) $schemes);
        $this->compiled = null;

        return $this;
    }

    /**
     * Checks if a scheme requirement has been set.
     */
    public function hasScheme(string $scheme): bool
    {
        return \in_array(strtolower($scheme), $this->schemes, true);
    }

    /**
     * Returns the uppercased HTTP methods this route is restricted to.
     * So an empty array means that any method is allowed.
     *
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Sets the HTTP methods (e.g. 'POST') this route is restricted to.
     * So an empty array means that any method is allowed.
     *
     * @param string|string[] $methods The method or an array of methods
     *
     * @return $this
     */
    public function setMethods(string|array $methods): static
    {
        $this->methods = array_map('strtoupper', (array) $methods);
        $this->compiled = null;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return $this
     */
    public function setOptions(array $options): static
    {
        $this->options = [
            'compiler_class' => RouteCompiler::class,
        ];

        return $this->addOptions($options);
    }

    /**
     * @return $this
     */
    public function addOptions(array $options): static
    {
        foreach ($options as $name => $option) {
            $this->options[$name] = $option;
        }
        $this->compiled = null;

        return $this;
    }

    /**
     * Sets an option value.
     *
     * @return $this
     */
    public function setOption(string $name, mixed $value): static
    {
        $this->options[$name] = $value;
        $this->compiled = null;

        return $this;
    }

    /**
     * Returns the option value or null when not found.
     */
    public function getOption(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }

    public function hasOption(string $name): bool
    {
        return \array_key_exists($name, $this->options);
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @return $this
     */
    public function setDefaults(array $defaults): static
    {
        $this->defaults = [];

        return $this->addDefaults($defaults);
    }

    /**
     * @return $this
     */
    public function addDefaults(array $defaults): static
    {
        if (isset($defaults['_locale']) && $this->isLocalized()) {
            unset($defaults['_locale']);
        }

        foreach ($defaults as $name => $default) {
            $this->defaults[$name] = $default;
        }
        $this->compiled = null;

        return $this;
    }

    public function getDefault(string $name): mixed
    {
        return $this->defaults[$name] ?? null;
    }

    public function hasDefault(string $name): bool
    {
        return \array_key_exists($name, $this->defaults);
    }

    /**
     * @return $this
     */
    public function setDefault(string $name, mixed $default): static
    {
        if ('_locale' === $name && $this->isLocalized()) {
            return $this;
        }

        $this->defaults[$name] = $default;
        $this->compiled = null;

        return $this;
    }

    public function getRequirements(): array
    {
        return $this->requirements;
    }

    /**
     * @return $this
     */
    public function setRequirements(array $requirements): static
    {
        $this->requirements = [];

        return $this->addRequirements($requirements);
    }

    /**
     * @return $this
     */
    public function addRequirements(array $requirements): static
    {
        if (isset($requirements['_locale']) && $this->isLocalized()) {
            unset($requirements['_locale']);
        }

        foreach ($requirements as $key => $regex) {
            $this->requirements[$key] = $this->sanitizeRequirement($key, $regex);
        }
        $this->compiled = null;

        return $this;
    }

    public function getRequirement(string $key): ?string
    {
        return $this->requirements[$key] ?? null;
    }

    public function hasRequirement(string $key): bool
    {
        return \array_key_exists($key, $this->requirements);
    }

    /**
     * @return $this
     */
    public function setRequirement(string $key, string $regex): static
    {
        if ('_locale' === $key && $this->isLocalized()) {
            return $this;
        }

        $this->requirements[$key] = $this->sanitizeRequirement($key, $regex);
        $this->compiled = null;

        return $this;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return $this
     */
    public function setCondition(?string $condition): static
    {
        $this->condition = (string) $condition;
        $this->compiled = null;

        return $this;
    }

    /**
     * Compiles the route.
     *
     * @throws \LogicException If the Route cannot be compiled because the
     *                         path or host pattern is invalid
     *
     * @see RouteCompiler which is responsible for the compilation process
     */
    public function compile(): CompiledRoute
    {
        if (null !== $this->compiled) {
            return $this->compiled;
        }

        $class = $this->getOption('compiler_class');

        return $this->compiled = $class::compile($this);
    }

    private function extractInlineDefaultsAndRequirements(string $pattern): string
    {
        if (false === strpbrk($pattern, '?<:')) {
            return $pattern;
        }

        $mapping = $this->getDefault('_route_mapping') ?? [];

        $pattern = preg_replace_callback('#\{(!?)([\w\x80-\xFF]++)(:([\w\x80-\xFF]++)(\.[\w\x80-\xFF]++)?)?(<.*?>)?(\?[^\}]*+)?\}#', function ($m) use (&$mapping) {
            if (isset($m[7][0])) {
                $this->setDefault($m[2], '?' !== $m[7] ? substr($m[7], 1) : null);
            }
            if (isset($m[6][0])) {
                $this->setRequirement($m[2], substr($m[6], 1, -1));
            }
            if (isset($m[4][0])) {
                $mapping[$m[2]] = isset($m[5][0]) ? [$m[4], substr($m[5], 1)] : $m[4];
            }

            return '{'.$m[1].$m[2].'}';
        }, $pattern);

        if ($mapping) {
            $this->setDefault('_route_mapping', $mapping);
        }

        return $pattern;
    }

    private function sanitizeRequirement(string $key, string $regex): string
    {
        if ('' !== $regex) {
            if ('^' === $regex[0]) {
                $regex = substr($regex, 1);
            } elseif (str_starts_with($regex, '\\A')) {
                $regex = substr($regex, 2);
            }
        }

        if (str_ends_with($regex, '$')) {
            $regex = substr($regex, 0, -1);
        } elseif (\strlen($regex) - 2 === strpos($regex, '\\z')) {
            $regex = substr($regex, 0, -2);
        }

        if ('' === $regex) {
            throw new \InvalidArgumentException(\sprintf('Routing requirement for "%s" cannot be empty.', $key));
        }

        return $regex;
    }

    private function isLocalized(): bool
    {
        return isset($this->defaults['_locale']) && isset($this->defaults['_canonical_route']) && ($this->requirements['_locale'] ?? null) === preg_quote($this->defaults['_locale']);
    }
}
