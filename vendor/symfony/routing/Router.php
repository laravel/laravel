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

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Symfony\Component\Routing\Generator\ConfigurableRequirementsInterface;
use Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper;
use Symfony\Component\Routing\Generator\Dumper\GeneratorDumperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumperInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * The Router class is an example of the integration of all pieces of the
 * routing system for easier use.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Router implements RouterInterface, RequestMatcherInterface
{
    protected UrlMatcherInterface|RequestMatcherInterface $matcher;
    protected UrlGeneratorInterface $generator;
    protected RequestContext $context;
    protected RouteCollection $collection;
    protected array $options = [];

    private ConfigCacheFactoryInterface $configCacheFactory;

    /**
     * @var ExpressionFunctionProviderInterface[]
     */
    private array $expressionLanguageProviders = [];

    private static ?array $cache = [];

    public function __construct(
        protected LoaderInterface $loader,
        protected mixed $resource,
        array $options = [],
        ?RequestContext $context = null,
        protected ?LoggerInterface $logger = null,
        protected ?string $defaultLocale = null,
    ) {
        $this->context = $context ?? new RequestContext();
        $this->setOptions($options);
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * cache_dir:              The cache directory (or null to disable caching)
     *   * debug:                  Whether to enable debugging or not (false by default)
     *   * generator_class:        The name of a UrlGeneratorInterface implementation
     *   * generator_dumper_class: The name of a GeneratorDumperInterface implementation
     *   * matcher_class:          The name of a UrlMatcherInterface implementation
     *   * matcher_dumper_class:   The name of a MatcherDumperInterface implementation
     *   * resource_type:          Type hint for the main resource (optional)
     *   * strict_requirements:    Configure strict requirement checking for generators
     *                             implementing ConfigurableRequirementsInterface (default is true)
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options): void
    {
        $this->options = [
            'cache_dir' => null,
            'debug' => false,
            'generator_class' => CompiledUrlGenerator::class,
            'generator_dumper_class' => CompiledUrlGeneratorDumper::class,
            'matcher_class' => CompiledUrlMatcher::class,
            'matcher_dumper_class' => CompiledUrlMatcherDumper::class,
            'resource_type' => null,
            'strict_requirements' => true,
        ];

        // check option names and live merge, if errors are encountered Exception will be thrown
        $invalid = [];
        foreach ($options as $key => $value) {
            if (\array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(\sprintf('The Router does not support the following options: "%s".', implode('", "', $invalid)));
        }
    }

    /**
     * Sets an option.
     *
     * @throws \InvalidArgumentException
     */
    public function setOption(string $key, mixed $value): void
    {
        if (!\array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(\sprintf('The Router does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }

    /**
     * Gets an option value.
     *
     * @throws \InvalidArgumentException
     */
    public function getOption(string $key): mixed
    {
        if (!\array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(\sprintf('The Router does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->collection ??= $this->loader->load($this->resource, $this->options['resource_type']);
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;

        if (isset($this->matcher)) {
            $this->getMatcher()->setContext($context);
        }
        if (isset($this->generator)) {
            $this->getGenerator()->setContext($context);
        }
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * Sets the ConfigCache factory to use.
     */
    public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory): void
    {
        $this->configCacheFactory = $configCacheFactory;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->getGenerator()->generate($name, $parameters, $referenceType);
    }

    public function match(string $pathinfo): array
    {
        return $this->getMatcher()->match($pathinfo);
    }

    public function matchRequest(Request $request): array
    {
        $matcher = $this->getMatcher();
        if (!$matcher instanceof RequestMatcherInterface) {
            // fallback to the default UrlMatcherInterface
            return $matcher->match($request->getPathInfo());
        }

        return $matcher->matchRequest($request);
    }

    /**
     * Gets the UrlMatcher or RequestMatcher instance associated with this Router.
     */
    public function getMatcher(): UrlMatcherInterface|RequestMatcherInterface
    {
        if (isset($this->matcher)) {
            return $this->matcher;
        }

        if (null === $this->options['cache_dir']) {
            $routes = $this->getRouteCollection();
            $compiled = is_a($this->options['matcher_class'], CompiledUrlMatcher::class, true);
            if ($compiled) {
                $routes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
            }
            $this->matcher = new $this->options['matcher_class']($routes, $this->context);
            if (method_exists($this->matcher, 'addExpressionLanguageProvider')) {
                foreach ($this->expressionLanguageProviders as $provider) {
                    $this->matcher->addExpressionLanguageProvider($provider);
                }
            }

            return $this->matcher;
        }

        $cache = $this->getConfigCacheFactory()->cache($this->options['cache_dir'].'/url_matching_routes.php',
            function (ConfigCacheInterface $cache) {
                $dumper = $this->getMatcherDumperInstance();
                if (method_exists($dumper, 'addExpressionLanguageProvider')) {
                    foreach ($this->expressionLanguageProviders as $provider) {
                        $dumper->addExpressionLanguageProvider($provider);
                    }
                }

                $cache->write($dumper->dump(), $this->getRouteCollection()->getResources());
                unset(self::$cache[$cache->getPath()]);
            }
        );

        return $this->matcher = new $this->options['matcher_class'](self::getCompiledRoutes($cache->getPath()), $this->context);
    }

    /**
     * Gets the UrlGenerator instance associated with this Router.
     */
    public function getGenerator(): UrlGeneratorInterface
    {
        if (isset($this->generator)) {
            return $this->generator;
        }

        if (null === $this->options['cache_dir']) {
            $routes = $this->getRouteCollection();
            $compiled = is_a($this->options['generator_class'], CompiledUrlGenerator::class, true);
            if ($compiled) {
                $generatorDumper = new CompiledUrlGeneratorDumper($routes);
                $routes = array_merge($generatorDumper->getCompiledRoutes(), $generatorDumper->getCompiledAliases());
            }
            $this->generator = new $this->options['generator_class']($routes, $this->context, $this->logger, $this->defaultLocale);
        } else {
            $cache = $this->getConfigCacheFactory()->cache($this->options['cache_dir'].'/url_generating_routes.php',
                function (ConfigCacheInterface $cache) {
                    $dumper = $this->getGeneratorDumperInstance();

                    $cache->write($dumper->dump(), $this->getRouteCollection()->getResources());
                    unset(self::$cache[$cache->getPath()]);
                }
            );

            $this->generator = new $this->options['generator_class'](self::getCompiledRoutes($cache->getPath()), $this->context, $this->logger, $this->defaultLocale);
        }

        if ($this->generator instanceof ConfigurableRequirementsInterface) {
            $this->generator->setStrictRequirements($this->options['strict_requirements']);
        }

        return $this->generator;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider): void
    {
        $this->expressionLanguageProviders[] = $provider;
    }

    protected function getGeneratorDumperInstance(): GeneratorDumperInterface
    {
        return new $this->options['generator_dumper_class']($this->getRouteCollection());
    }

    protected function getMatcherDumperInstance(): MatcherDumperInterface
    {
        return new $this->options['matcher_dumper_class']($this->getRouteCollection());
    }

    /**
     * Provides the ConfigCache factory implementation, falling back to a
     * default implementation if necessary.
     */
    private function getConfigCacheFactory(): ConfigCacheFactoryInterface
    {
        return $this->configCacheFactory ??= new ConfigCacheFactory($this->options['debug']);
    }

    private static function getCompiledRoutes(string $path): array
    {
        if ([] === self::$cache && \function_exists('opcache_invalidate') && filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOL) && (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) || filter_var(\ini_get('opcache.enable_cli'), \FILTER_VALIDATE_BOOL))) {
            self::$cache = null;
        }

        if (null === self::$cache) {
            return require $path;
        }

        return self::$cache[$path] ??= require $path;
    }
}
