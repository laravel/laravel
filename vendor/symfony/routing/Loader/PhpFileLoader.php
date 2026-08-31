<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\Exception\InvalidArgumentException;
use Symfony\Component\Routing\Loader\Configurator\Routes;
use Symfony\Component\Routing\Loader\Configurator\RoutesReference;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollection;

/**
 * PhpFileLoader loads routes from a PHP file.
 *
 * The file must return a RouteCollection instance.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Nicolas grekas <p@tchwork.com>
 * @author Jules Pietri <jules@heahprod.com>
 */
class PhpFileLoader extends FileLoader
{
    /**
     * Loads a PHP file.
     */
    public function load(mixed $file, ?string $type = null): RouteCollection
    {
        $path = $this->locator->locate($file);
        $this->setCurrentDir(\dirname($path));

        // Expose RoutesReference::config() as Routes::config()
        if (!class_exists(Routes::class)) {
            class_alias(RoutesReference::class, Routes::class);
        }

        // the closure forbids access to the private scope in the included file
        $loader = $this;
        $load = \Closure::bind(static function ($file) use ($loader) {
            return include $file;
        }, null, null);

        try {
            if (1 === $result = $load($path)) {
                $result = null;
            }
        } catch (\Error $e) {
            $load = \Closure::bind(static function ($file) use ($loader) {
                return include $file;
            }, null, ProtectedPhpFileLoader::class);

            if (1 === $result = $load($path)) {
                $result = null;
            }

            trigger_deprecation('symfony/routing', '7.4', 'Accessing the internal scope of the loader in config files is deprecated, use only its public API instead in "%s" on line %d.', $e->getFile(), $e->getLine());
        }

        if (\is_object($result) && \is_callable($result)) {
            $collection = $this->callConfigurator($result, $path, $file);
        } elseif (\is_array($result)) {
            $collection = new RouteCollection();
            $loader = new YamlFileLoader($this->locator, $this->env);
            $loader->setResolver($this->resolver ?? new LoaderResolver([$this]));
            (new \ReflectionMethod(YamlFileLoader::class, 'loadContent'))->invoke($loader, $collection, $result, $path, $file);
        } elseif (!($collection = $result) instanceof RouteCollection) {
            throw new InvalidArgumentException(\sprintf('The return value in config file "%s" is expected to be a RouteCollection, an array or a configurator callable, but got "%s".', $path, get_debug_type($result)));
        }

        $collection->addResource(new FileResource($path));

        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return \is_string($resource) && 'php' === pathinfo($resource, \PATHINFO_EXTENSION) && (!$type || 'php' === $type);
    }

    protected function callConfigurator(callable $callback, string $path, string $file): RouteCollection
    {
        $collection = new RouteCollection();

        $callback(new RoutingConfigurator($collection, $this, $path, $file, $this->env));

        return $collection;
    }
}

/**
 * @internal
 */
final class ProtectedPhpFileLoader extends PhpFileLoader
{
}
