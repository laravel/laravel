<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

use Symfony\Component\Config\Builder\ConfigBuilderGenerator;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\RemoveBuildParametersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\Preloader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;

// Help opcache.preload discover always-needed symbols
class_exists(ConfigCache::class);

/**
 * The Kernel is the heart of the Symfony system.
 *
 * It manages an environment made of bundles.
 *
 * Environment names must always start with a letter and
 * they must only contain letters and numbers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Kernel implements KernelInterface, RebootableInterface, TerminableInterface
{
    /**
     * @var array<string, BundleInterface>
     */
    protected array $bundles = [];

    protected ?ContainerInterface $container = null;
    protected bool $booted = false;
    protected ?float $startTime = null;

    private string $projectDir;
    private ?string $warmupDir = null;
    private int $requestStackSize = 0;
    private bool $resetServices = false;
    private bool $handlingHttpCache = false;

    /**
     * @var array<string, bool>
     */
    private static array $freshCache = [];

    public const VERSION = '7.4.14';
    public const VERSION_ID = 70414;
    public const MAJOR_VERSION = 7;
    public const MINOR_VERSION = 4;
    public const RELEASE_VERSION = 14;
    public const EXTRA_VERSION = '';

    public const END_OF_MAINTENANCE = '11/2028';
    public const END_OF_LIFE = '11/2029';

    public function __construct(
        protected string $environment,
        protected bool $debug,
    ) {
        if (!$environment) {
            throw new \InvalidArgumentException(\sprintf('Invalid environment provided to "%s": the environment cannot be empty.', get_debug_type($this)));
        }
    }

    public function __clone()
    {
        $this->booted = false;
        $this->container = null;
        $this->requestStackSize = 0;
        $this->resetServices = false;
        $this->handlingHttpCache = false;
    }

    public function boot(): void
    {
        if ($this->booted) {
            if (!$this->requestStackSize && $this->resetServices) {
                if ($this->container->has('services_resetter')) {
                    $this->container->get('services_resetter')->reset();
                }
                $this->resetServices = false;
                if ($this->debug) {
                    $this->startTime = microtime(true);
                }
            }

            return;
        }

        if (!$this->container) {
            $this->preBoot();
        }

        foreach ($this->getBundles() as $bundle) {
            $bundle->setContainer($this->container);
            $bundle->boot();
        }

        $this->booted = true;
    }

    public function reboot(?string $warmupDir): void
    {
        $this->shutdown();
        $this->warmupDir = $warmupDir;
        $this->boot();
    }

    public function terminate(Request $request, Response $response): void
    {
        if (!$this->booted) {
            return;
        }

        if ($this->getHttpKernel() instanceof TerminableInterface) {
            $this->getHttpKernel()->terminate($request, $response);
        }
    }

    public function shutdown(): void
    {
        if (!$this->booted) {
            return;
        }

        $this->booted = false;

        foreach ($this->getBundles() as $bundle) {
            $bundle->shutdown();
            $bundle->setContainer(null);
        }

        $this->container = null;
        $this->requestStackSize = 0;
        $this->resetServices = false;
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        if (!$this->container) {
            $this->preBoot();
        }

        if (HttpKernelInterface::MAIN_REQUEST === $type && !$this->handlingHttpCache && $this->container->has('http_cache')) {
            $this->handlingHttpCache = true;

            try {
                return $this->container->get('http_cache')->handle($request, $type, $catch);
            } finally {
                $this->handlingHttpCache = false;
                $this->resetServices = true;
            }
        }

        $this->boot();
        ++$this->requestStackSize;
        if (!$this->handlingHttpCache) {
            $this->resetServices = true;
        }

        try {
            return $this->getHttpKernel()->handle($request, $type, $catch);
        } finally {
            --$this->requestStackSize;
        }
    }

    /**
     * Gets an HTTP kernel from the container.
     */
    protected function getHttpKernel(): HttpKernelInterface
    {
        return $this->container->get('http_kernel');
    }

    public function getBundles(): array
    {
        return $this->bundles;
    }

    public function getBundle(string $name): BundleInterface
    {
        if (!isset($this->bundles[$name])) {
            throw new \InvalidArgumentException(\sprintf('Bundle "%s" does not exist or it is not enabled. Maybe you forgot to add it in the "registerBundles()" method of your "%s.php" file?', $name, get_debug_type($this)));
        }

        return $this->bundles[$name];
    }

    public function locateResource(string $name): string
    {
        if ('@' !== $name[0]) {
            throw new \InvalidArgumentException(\sprintf('A resource name must start with @ ("%s" given).', $name));
        }

        if (str_contains($name, '..')) {
            throw new \RuntimeException(\sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $bundleName = substr($name, 1);
        $path = '';
        if (str_contains($bundleName, '/')) {
            [$bundleName, $path] = explode('/', $bundleName, 2);
        }

        $bundle = $this->getBundle($bundleName);
        if (file_exists($file = $bundle->getPath().'/'.$path)) {
            return $file;
        }

        throw new \InvalidArgumentException(\sprintf('Unable to find file "%s".', $name));
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * Gets the application root dir (path of the project's composer file).
     */
    public function getProjectDir(): string
    {
        if (!isset($this->projectDir)) {
            $r = new \ReflectionObject($this);

            if (!is_file($dir = $r->getFileName())) {
                throw new \LogicException(\sprintf('Cannot auto-detect project dir for kernel of class "%s".', $r->name));
            }

            $dir = $rootDir = \dirname($dir);
            while (!is_file($dir.'/composer.json')) {
                if ($dir === \dirname($dir)) {
                    return $this->projectDir = $rootDir;
                }
                $dir = \dirname($dir);
            }
            $this->projectDir = $dir;
        }

        return $this->projectDir;
    }

    public function getContainer(): ContainerInterface
    {
        if (!$this->container) {
            throw new \LogicException('Cannot retrieve the container from a non-booted kernel.');
        }

        return $this->container;
    }

    /**
     * @internal
     *
     * @deprecated since Symfony 7.1, to be removed in 8.0
     */
    public function setAnnotatedClassCache(array $annotatedClasses): void
    {
        trigger_deprecation('symfony/http-kernel', '7.1', 'The "%s()" method is deprecated since Symfony 7.1 and will be removed in 8.0.', __METHOD__);

        file_put_contents(($this->warmupDir ?: $this->getBuildDir()).'/annotations.map', \sprintf('<?php return %s;', var_export($annotatedClasses, true)));
    }

    public function getStartTime(): float
    {
        return $this->debug && null !== $this->startTime ? $this->startTime : -\INF;
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getBuildDir(): string
    {
        // Returns $this->getCacheDir() for backward compatibility
        return $this->getCacheDir();
    }

    public function getShareDir(): ?string
    {
        // Returns $this->getCacheDir() for backward compatibility
        return $this->getCacheDir();
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    public function getCharset(): string
    {
        return 'UTF-8';
    }

    /**
     * Gets the patterns defining the classes to parse and cache for annotations.
     *
     * @return string[]
     *
     * @deprecated since Symfony 7.1, to be removed in 8.0
     */
    public function getAnnotatedClassesToCompile(): array
    {
        trigger_deprecation('symfony/http-kernel', '7.1', 'The "%s()" method is deprecated since Symfony 7.1 and will be removed in 8.0.', __METHOD__);

        return [];
    }

    /**
     * Initializes bundles.
     *
     * @throws \LogicException if two bundles share a common name
     */
    protected function initializeBundles(): void
    {
        // init bundles
        $this->bundles = [];
        foreach ($this->registerBundles() as $bundle) {
            $name = $bundle->getName();
            if (isset($this->bundles[$name])) {
                throw new \LogicException(\sprintf('Trying to register two bundles with the same name "%s".', $name));
            }
            $this->bundles[$name] = $bundle;
        }
    }

    /**
     * The extension point similar to the Bundle::build() method.
     *
     * Use this method to register compiler passes and manipulate the container during the building process.
     */
    protected function build(ContainerBuilder $container): void
    {
    }

    /**
     * Gets the container class.
     *
     * @throws \InvalidArgumentException If the generated classname is invalid
     */
    protected function getContainerClass(): string
    {
        $class = static::class;
        $class = str_contains($class, "@anonymous\0") ? get_parent_class($class).str_replace('.', '_', ContainerBuilder::hash($class)) : $class;
        $class = str_replace('\\', '_', $class).ucfirst($this->environment).($this->debug ? 'Debug' : '').'Container';

        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $class)) {
            throw new \InvalidArgumentException(\sprintf('The environment "%s" contains invalid characters, it can only contain characters allowed in PHP class names.', $this->environment));
        }

        return $class;
    }

    /**
     * Gets the container's base class.
     *
     * All names except Container must be fully qualified.
     */
    protected function getContainerBaseClass(): string
    {
        return 'Container';
    }

    /**
     * Initializes the service container.
     *
     * The built version of the service container is used when fresh, otherwise the
     * container is built.
     */
    protected function initializeContainer(): void
    {
        $class = $this->getContainerClass();
        $buildDir = $this->warmupDir ?: $this->getBuildDir();
        $skip = $_SERVER['SYMFONY_DISABLE_RESOURCE_TRACKING'] ?? '';
        $skip = filter_var($skip, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE) ?? explode(',', $skip);
        $cache = new ConfigCache($buildDir.'/'.$class.'.php', $this->debug, null, \is_array($skip) && ['*'] !== $skip ? $skip : ($skip ? [] : null));

        $cachePath = $cache->getPath();

        // Silence E_WARNING to ignore "include" failures - don't use "@" to prevent silencing fatal errors
        $errorLevel = error_reporting();
        error_reporting($errorLevel & ~\E_WARNING);

        try {
            if (is_file($cachePath) && \is_object($this->container = include $cachePath)
                && (!$this->debug || (self::$freshCache[$cachePath] ?? $cache->isFresh()))
            ) {
                self::$freshCache[$cachePath] = true;
                $this->container->set('kernel', $this);
                error_reporting($errorLevel);

                return;
            }
        } catch (\Throwable $e) {
        }

        $oldContainer = \is_object($this->container) ? new \ReflectionClass($this->container) : $this->container = null;

        try {
            is_dir($buildDir) ?: mkdir($buildDir, 0o777, true);

            if ($lock = fopen($cachePath.'.lock', 'w+')) {
                if (!flock($lock, \LOCK_EX | \LOCK_NB, $wouldBlock) && !flock($lock, $wouldBlock ? \LOCK_SH : \LOCK_EX)) {
                    fclose($lock);
                    $lock = null;
                } elseif (!is_file($cachePath) || !\is_object($this->container = include $cachePath)) {
                    $this->container = null;
                } elseif (!$oldContainer || $this->container::class !== $oldContainer->name) {
                    flock($lock, \LOCK_UN);
                    fclose($lock);
                    $this->container->set('kernel', $this);

                    return;
                }
            }
        } catch (\Throwable $e) {
        } finally {
            error_reporting($errorLevel);
        }

        if ($collectDeprecations = $this->debug && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $collectedLogs = [];
            $previousHandler = set_error_handler(static function ($type, $message, $file, $line) use (&$collectedLogs, &$previousHandler) {
                if (\E_USER_DEPRECATED !== $type && \E_DEPRECATED !== $type) {
                    return $previousHandler ? $previousHandler($type, $message, $file, $line) : false;
                }

                if (isset($collectedLogs[$message])) {
                    ++$collectedLogs[$message]['count'];

                    return null;
                }

                $backtrace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 5);
                // Clean the trace by removing first frames added by the error handler itself.
                for ($i = 0; isset($backtrace[$i]); ++$i) {
                    if (isset($backtrace[$i]['file'], $backtrace[$i]['line']) && $backtrace[$i]['line'] === $line && $backtrace[$i]['file'] === $file) {
                        $backtrace = \array_slice($backtrace, 1 + $i);
                        break;
                    }
                }
                for ($i = 0; isset($backtrace[$i]); ++$i) {
                    if (!isset($backtrace[$i]['file'], $backtrace[$i]['line'], $backtrace[$i]['function'])) {
                        continue;
                    }
                    if (!isset($backtrace[$i]['class']) && 'trigger_deprecation' === $backtrace[$i]['function']) {
                        $file = $backtrace[$i]['file'];
                        $line = $backtrace[$i]['line'];
                        $backtrace = \array_slice($backtrace, 1 + $i);
                        break;
                    }
                }

                // Remove frames added by DebugClassLoader.
                for ($i = \count($backtrace) - 2; 0 < $i; --$i) {
                    if (DebugClassLoader::class === ($backtrace[$i]['class'] ?? null)) {
                        $backtrace = [$backtrace[$i + 1]];
                        break;
                    }
                }

                $collectedLogs[$message] = [
                    'type' => $type,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line,
                    'trace' => [$backtrace[0]],
                    'count' => 1,
                ];

                return null;
            });
        }

        try {
            $container = null;
            $container = $this->buildContainer();
            $container->compile();
        } finally {
            if ($collectDeprecations) {
                restore_error_handler();

                @file_put_contents($buildDir.'/'.$class.'Deprecations.log', serialize(array_values($collectedLogs)));
                @file_put_contents($buildDir.'/'.$class.'Compiler.log', null !== $container ? implode("\n", $container->getCompiler()->getLog()) : '');
            }
        }

        $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

        if ($lock) {
            flock($lock, \LOCK_UN);
            fclose($lock);
        }

        $this->container = require $cachePath;
        $this->container->set('kernel', $this);

        if ($oldContainer && $this->container::class !== $oldContainer->name) {
            // Because concurrent requests might still be using them,
            // old container files are not removed immediately,
            // but on a next dump of the container.
            static $legacyContainers = [];
            $oldContainerDir = \dirname($oldContainer->getFileName());
            $legacyContainers[$oldContainerDir.'.legacy'] = true;
            foreach (glob(\dirname($oldContainerDir).\DIRECTORY_SEPARATOR.'*.legacy', \GLOB_NOSORT) as $legacyContainer) {
                if (!isset($legacyContainers[$legacyContainer]) && @unlink($legacyContainer)) {
                    (new Filesystem())->remove(substr($legacyContainer, 0, -7));
                }
            }

            touch($oldContainerDir.'.legacy');
        }

        $buildDir = $this->container->getParameter('kernel.build_dir');
        $cacheDir = $this->container->getParameter('kernel.cache_dir');
        $preload = $this instanceof WarmableInterface ? $this->warmUp($cacheDir, $buildDir) : [];

        if ($this->container->has('cache_warmer')) {
            $cacheWarmer = $this->container->get('cache_warmer');

            if ($cacheDir !== $buildDir) {
                $cacheWarmer->enableOptionalWarmers();
            }

            $preload = array_merge($preload, $cacheWarmer->warmUp($cacheDir, $buildDir));
        }

        if ($preload && file_exists($preloadFile = $buildDir.'/'.$class.'.preload.php')) {
            Preloader::append($preloadFile, $preload);
        }
    }

    /**
     * Returns the kernel parameters.
     *
     * @return array<string, array|bool|string|int|float|\UnitEnum|null>
     */
    protected function getKernelParameters(): array
    {
        $bundles = [];
        $bundlesMetadata = [];

        foreach ($this->bundles as $name => $bundle) {
            $bundles[$name] = $bundle::class;
            $bundlesMetadata[$name] = [
                'path' => $bundle->getPath(),
                'namespace' => $bundle->getNamespace(),
            ];
        }

        return [
            'kernel.project_dir' => realpath($this->getProjectDir()) ?: $this->getProjectDir(),
            'kernel.environment' => $this->environment,
            'kernel.runtime_environment' => '%env(default:kernel.environment:APP_RUNTIME_ENV)%',
            'kernel.runtime_mode' => '%env(query_string:default:container.runtime_mode:APP_RUNTIME_MODE)%',
            'kernel.runtime_mode.web' => '%env(bool:default::key:web:default:kernel.runtime_mode:)%',
            'kernel.runtime_mode.cli' => '%env(not:default:kernel.runtime_mode.web:)%',
            'kernel.runtime_mode.worker' => '%env(bool:default::key:worker:default:kernel.runtime_mode:)%',
            'kernel.debug' => $this->debug,
            'kernel.build_dir' => realpath($dir = $this->warmupDir ?: $this->getBuildDir()) ?: $dir,
            'kernel.cache_dir' => realpath($dir = ($this->getCacheDir() === $this->getBuildDir() ? ($this->warmupDir ?: $this->getCacheDir()) : $this->getCacheDir())) ?: $dir,
            'kernel.logs_dir' => realpath($dir = $this->getLogDir()) ?: $dir,
            'kernel.bundles' => $bundles,
            'kernel.bundles_metadata' => $bundlesMetadata,
            'kernel.charset' => $this->getCharset(),
            'kernel.container_class' => $this->getContainerClass(),
        ] + (null !== ($dir = $this->getShareDir()) ? ['kernel.share_dir' => realpath($dir) ?: $dir] : []);
    }

    /**
     * Builds the service container.
     *
     * @throws \RuntimeException
     */
    protected function buildContainer(): ContainerBuilder
    {
        foreach (['cache' => $this->getCacheDir(), 'build' => $this->warmupDir ?: $this->getBuildDir()] as $name => $dir) {
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0o777, true) && !is_dir($dir)) {
                    throw new \RuntimeException(\sprintf('Unable to create the "%s" directory (%s).', $name, $dir));
                }
            } elseif (!is_writable($dir)) {
                throw new \RuntimeException(\sprintf('Unable to write in the "%s" directory (%s).', $name, $dir));
            }
        }

        $container = $this->getContainerBuilder();
        $container->addObjectResource($this);
        $this->prepareContainer($container);
        $this->registerContainerConfiguration($this->getContainerLoader($container));

        return $container;
    }

    /**
     * Prepares the ContainerBuilder before it is compiled.
     */
    protected function prepareContainer(ContainerBuilder $container): void
    {
        $extensions = [];
        foreach ($this->bundles as $bundle) {
            if ($extension = $bundle->getContainerExtension()) {
                $container->registerExtension($extension);
            }

            if ($this->debug) {
                $container->addObjectResource($bundle);
            }
        }

        foreach ($this->bundles as $bundle) {
            $bundle->build($container);
        }

        $this->build($container);

        foreach ($container->getExtensions() as $extension) {
            $extensions[] = $extension->getAlias();
        }

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));
    }

    /**
     * Gets a new ContainerBuilder instance used to build the service container.
     */
    protected function getContainerBuilder(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->getParameterBag()->add($this->getKernelParameters());

        if ($this instanceof ExtensionInterface) {
            $container->registerExtension($this);
        }
        if ($this instanceof CompilerPassInterface) {
            $container->addCompilerPass($this, PassConfig::TYPE_BEFORE_OPTIMIZATION, -10000);
        }

        return $container;
    }

    /**
     * Dumps the service container to PHP code in the cache.
     *
     * @param string $class     The name of the class to generate
     * @param string $baseClass The name of the container's base class
     */
    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, string $class, string $baseClass): void
    {
        // cache the container
        $dumper = new PhpDumper($container);

        $buildParameters = [];
        foreach ($container->getCompilerPassConfig()->getPasses() as $pass) {
            if ($pass instanceof RemoveBuildParametersPass) {
                $buildParameters = array_merge($buildParameters, $pass->getRemovedParameters());
            }
        }

        $content = $dumper->dump([
            'class' => $class,
            'base_class' => $baseClass,
            'file' => $cache->getPath(),
            'as_files' => true,
            'debug' => $this->debug,
            'inline_factories' => $buildParameters['.container.dumper.inline_factories'] ?? false,
            'inline_class_loader' => $buildParameters['.container.dumper.inline_class_loader'] ?? $this->debug,
            'build_time' => $container->hasParameter('kernel.container_build_time') ? $container->getParameter('kernel.container_build_time') : time(),
            'preload_classes' => array_map('get_class', $this->bundles),
        ]);

        $rootCode = array_pop($content);
        $dir = \dirname($cache->getPath()).'/';
        $fs = new Filesystem();

        foreach ($content as $file => $code) {
            $fs->dumpFile($dir.$file, $code);
            @chmod($dir.$file, 0o666 & ~umask());
        }
        $legacyFile = \dirname($dir.key($content)).'.legacy';
        if (is_file($legacyFile)) {
            @unlink($legacyFile);
        }

        $cache->write($rootCode, $container->getResources());
    }

    /**
     * Returns a loader for the container.
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $env = $this->getEnvironment();
        $locator = new FileLocator($this);
        $resolver = new LoaderResolver(array_merge(class_exists(XmlFileLoader::class) ? [
            new XmlFileLoader($container, $locator, $env),
        ] : [], [
            new YamlFileLoader($container, $locator, $env),
            new IniFileLoader($container, $locator, $env),
            class_exists(XmlFileLoader::class, false) && class_exists(ConfigBuilderGenerator::class) ? new PhpFileLoader($container, $locator, $env, new ConfigBuilderGenerator($this->getBuildDir())) : new PhpFileLoader($container, $locator, $env),
            new GlobFileLoader($container, $locator, $env),
            new DirectoryLoader($container, $locator, $env),
            new ClosureLoader($container, $env),
        ]));

        return new DelegatingLoader($resolver);
    }

    private function preBoot(): ContainerInterface
    {
        if ($this->debug) {
            $this->startTime = microtime(true);
        }
        if ($this->debug && !isset($_ENV['SHELL_VERBOSITY']) && !isset($_SERVER['SHELL_VERBOSITY'])) {
            if (\function_exists('putenv')) {
                putenv('SHELL_VERBOSITY=3');
            }
            $_ENV['SHELL_VERBOSITY'] = 3;
            $_SERVER['SHELL_VERBOSITY'] = 3;
        }

        $this->initializeBundles();
        $this->initializeContainer();

        $container = $this->container;

        if ($container->hasParameter('kernel.trusted_hosts') && $trustedHosts = $container->getParameter('kernel.trusted_hosts')) {
            Request::setTrustedHosts(\is_array($trustedHosts) ? $trustedHosts : preg_split('/\s*+,\s*+(?![^{]*})/', $trustedHosts));
        }

        if ($container->hasParameter('kernel.trusted_proxies') && $container->hasParameter('kernel.trusted_headers') && $trustedProxies = $container->getParameter('kernel.trusted_proxies')) {
            $trustedHeaders = $container->getParameter('kernel.trusted_headers');

            if (\is_string($trustedHeaders)) {
                $trustedHeaders = array_map('trim', explode(',', $trustedHeaders));
            }

            if (\is_array($trustedHeaders)) {
                $trustedHeaderSet = 0;

                foreach ($trustedHeaders as $header) {
                    if (!\defined($const = Request::class.'::HEADER_'.strtr(strtoupper($header), '-', '_'))) {
                        throw new \InvalidArgumentException(\sprintf('The trusted header "%s" is not supported.', $header));
                    }
                    $trustedHeaderSet |= \constant($const);
                }
            } else {
                $trustedHeaderSet = $trustedHeaders ?? (Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
            }

            Request::setTrustedProxies(\is_array($trustedProxies) ? $trustedProxies : array_map('trim', explode(',', $trustedProxies)), $trustedHeaderSet);
        }

        return $container;
    }

    public function __serialize(): array
    {
        if (self::class === (new \ReflectionMethod($this, '__sleep'))->class || self::class !== (new \ReflectionMethod($this, '__serialize'))->class) {
            return [
                'environment' => $this->environment,
                'debug' => $this->debug,
            ];
        }

        trigger_deprecation('symfony/http-kernel', '7.4', 'Implementing "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        $data = [];
        foreach ($this->__sleep() as $key) {
            try {
                if (($r = new \ReflectionProperty($this, $key))->isInitialized($this)) {
                    $data[$key] = $r->getValue($this);
                }
            } catch (\ReflectionException) {
                $data[$key] = $this->$key;
            }
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        if ($wakeup = self::class !== (new \ReflectionMethod($this, '__wakeup'))->class && self::class === (new \ReflectionMethod($this, '__unserialize'))->class) {
            trigger_deprecation('symfony/http-kernel', '7.4', 'Implementing "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));
        }

        if (\in_array(array_keys($data), [['environment', 'debug'], ["\0*\0environment", "\0*\0debug"]], true)) {
            $environment = $data['environment'] ?? $data["\0*\0environment"];
            $debug = $data['debug'] ?? $data["\0*\0debug"];

            if (\is_object($environment) || \is_object($debug)) {
                throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
            }

            $this->environment = $environment;
            $this->debug = $debug;

            if ($wakeup) {
                $this->__wakeup();
            } else {
                $this->__construct($environment, $debug);
            }

            return;
        }

        trigger_deprecation('symfony/http-kernel', '7.4', 'Passing more than just key "environment" and "debug" to "%s::__unserialize()" is deprecated, populate properties in "%s::__unserialize()" instead.', self::class, get_debug_type($this));

        \Closure::bind(function ($data) use ($wakeup) {
            foreach ($data as $key => $value) {
                $this->{("\0" === $key[0] ?? '') ? substr($key, 1 + strrpos($key, "\0")) : $key} = $value;
            }

            if ($wakeup) {
                $this->__wakeup();
            } else {
                if (\is_object($this->environment) || \is_object($this->debug)) {
                    throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
                }

                $this->__construct($this->environment, $this->debug);
            }
        }, $this, static::class)($data);
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__serialize()` in 8.0
     */
    public function __sleep(): array
    {
        trigger_deprecation('symfony/http-kernel', '7.4', 'Calling "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        return ['environment', 'debug'];
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__unserialize()` in 8.0
     */
    public function __wakeup(): void
    {
        trigger_deprecation('symfony/http-kernel', '7.4', 'Calling "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));

        if (\is_object($this->environment) || \is_object($this->debug)) {
            throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
        }

        $this->__construct($this->environment, $this->debug);
    }
}
