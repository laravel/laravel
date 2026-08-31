<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Attribute;

use Symfony\Component\Routing\Exception\LogicException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Alexander M. Turek <me@derrabus.de>
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Route
{
    /** @var string[] */
    public array $methods;

    /** @var string[] */
    public array $envs;

    /** @var string[] */
    public array $schemes;

    /** @var (string|DeprecatedAlias)[] */
    public array $aliases = [];

    /**
     * @param string|array<string,string>|null                  $path         The route path (i.e. "/user/login")
     * @param string|null                                       $name         The route name (i.e. "app_user_login")
     * @param array<string|\Stringable>                         $requirements Requirements for the route attributes, @see https://symfony.com/doc/current/routing.html#parameters-validation
     * @param array<string, mixed>                              $options      Options for the route (i.e. ['prefix' => '/api'])
     * @param array<string, mixed>                              $defaults     Default values for the route attributes and query parameters
     * @param string|null                                       $host         The host for which this route should be active (i.e. "localhost")
     * @param string|string[]                                   $methods      The list of HTTP methods allowed by this route
     * @param string|string[]                                   $schemes      The list of schemes allowed by this route (i.e. "https")
     * @param string|null                                       $condition    An expression that must evaluate to true for the route to be matched, @see https://symfony.com/doc/current/routing.html#matching-expressions
     * @param int|null                                          $priority     The priority of the route if multiple ones are defined for the same path
     * @param string|null                                       $locale       The locale accepted by the route
     * @param string|null                                       $format       The format returned by the route (i.e. "json", "xml")
     * @param bool|null                                         $utf8         Whether the route accepts UTF-8 in its parameters
     * @param bool|null                                         $stateless    Whether the route is defined as stateless or stateful, @see https://symfony.com/doc/current/routing.html#stateless-routes
     * @param string|string[]|null                              $env          The env(s) in which the route is defined (i.e. "dev", "test", "prod", ["dev", "test"])
     * @param string|DeprecatedAlias|(string|DeprecatedAlias)[] $alias        The list of aliases for this route
     */
    public function __construct(
        public string|array|null $path = null,
        public ?string $name = null,
        public array $requirements = [],
        public array $options = [],
        public array $defaults = [],
        public ?string $host = null,
        array|string $methods = [],
        array|string $schemes = [],
        public ?string $condition = null,
        public ?int $priority = null,
        ?string $locale = null,
        ?string $format = null,
        ?bool $utf8 = null,
        ?bool $stateless = null,
        string|array|null $env = null,
        string|DeprecatedAlias|array $alias = [],
    ) {
        $this->path = $path;
        $this->methods = (array) $methods;
        $this->schemes = (array) $schemes;
        $this->envs = (array) $env;
        $this->aliases = \is_array($alias) ? $alias : [$alias];

        if (null !== $locale) {
            $this->defaults['_locale'] = $locale;
        }

        if (null !== $format) {
            $this->defaults['_format'] = $format;
        }

        if (null !== $utf8) {
            $this->options['utf8'] = $utf8;
        }

        if (null !== $stateless) {
            $this->defaults['_stateless'] = $stateless;
        }
    }

    #[\Deprecated('Use the "path" property instead', 'symfony/routing:7.4')]
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    #[\Deprecated('Use the "path" property instead', 'symfony/routing:7.4')]
    public function getPath(): ?string
    {
        return \is_array($this->path) ? null : $this->path;
    }

    #[\Deprecated('Use the "path" property instead', 'symfony/routing:7.4')]
    public function setLocalizedPaths(array $localizedPaths): void
    {
        $this->path = $localizedPaths;
    }

    #[\Deprecated('Use the "path" property instead', 'symfony/routing:7.4')]
    public function getLocalizedPaths(): array
    {
        return \is_array($this->path) ? $this->path : [];
    }

    #[\Deprecated('Use the "host" property instead', 'symfony/routing:7.4')]
    public function setHost(string $pattern): void
    {
        $this->host = $pattern;
    }

    #[\Deprecated('Use the "host" property instead', 'symfony/routing:7.4')]
    public function getHost(): ?string
    {
        return $this->host;
    }

    #[\Deprecated('Use the "name" property instead', 'symfony/routing:7.4')]
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[\Deprecated('Use the "name" property instead', 'symfony/routing:7.4')]
    public function getName(): ?string
    {
        return $this->name;
    }

    #[\Deprecated('Use the "requirements" property instead', 'symfony/routing:7.4')]
    public function setRequirements(array $requirements): void
    {
        $this->requirements = $requirements;
    }

    #[\Deprecated('Use the "requirements" property instead', 'symfony/routing:7.4')]
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    #[\Deprecated('Use the "options" property instead', 'symfony/routing:7.4')]
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    #[\Deprecated('Use the "options" property instead', 'symfony/routing:7.4')]
    public function getOptions(): array
    {
        return $this->options;
    }

    #[\Deprecated('Use the "defaults" property instead', 'symfony/routing:7.4')]
    public function setDefaults(array $defaults): void
    {
        $this->defaults = $defaults;
    }

    #[\Deprecated('Use the "defaults" property instead', 'symfony/routing:7.4')]
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    #[\Deprecated('Use the "schemes" property instead', 'symfony/routing:7.4')]
    public function setSchemes(array|string $schemes): void
    {
        $this->schemes = (array) $schemes;
    }

    #[\Deprecated('Use the "schemes" property instead', 'symfony/routing:7.4')]
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    #[\Deprecated('Use the "methods" property instead', 'symfony/routing:7.4')]
    public function setMethods(array|string $methods): void
    {
        $this->methods = (array) $methods;
    }

    #[\Deprecated('Use the "methods" property instead', 'symfony/routing:7.4')]
    public function getMethods(): array
    {
        return $this->methods;
    }

    #[\Deprecated('Use the "condition" property instead', 'symfony/routing:7.4')]
    public function setCondition(?string $condition): void
    {
        $this->condition = $condition;
    }

    #[\Deprecated('Use the "condition" property instead', 'symfony/routing:7.4')]
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    #[\Deprecated('Use the "priority" property instead', 'symfony/routing:7.4')]
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    #[\Deprecated('Use the "priority" property instead', 'symfony/routing:7.4')]
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    #[\Deprecated('Use the "envs" property instead', 'symfony/routing:7.4')]
    public function setEnv(?string $env): void
    {
        $this->envs = (array) $env;
    }

    #[\Deprecated('Use the "envs" property instead', 'symfony/routing:7.4')]
    public function getEnv(): ?string
    {
        if (!$this->envs) {
            return null;
        }
        if (\count($this->envs) > 1) {
            throw new LogicException(\sprintf('The "env" property has %d environments. Use "getEnvs()" to get all of them.', \count($this->envs)));
        }

        return $this->envs[0];
    }

    /**
     * @return (string|DeprecatedAlias)[]
     */
    #[\Deprecated('Use the "aliases" property instead', 'symfony/routing:7.4')]
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param string|DeprecatedAlias|(string|DeprecatedAlias)[] $aliases
     */
    #[\Deprecated('Use the "aliases" property instead', 'symfony/routing:7.4')]
    public function setAliases(string|DeprecatedAlias|array $aliases): void
    {
        $this->aliases = \is_array($aliases) ? $aliases : [$aliases];
    }
}

if (!class_exists(\Symfony\Component\Routing\Annotation\Route::class, false)) {
    class_alias(Route::class, \Symfony\Component\Routing\Annotation\Route::class);
}
