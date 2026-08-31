<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader\Configurator\Traits;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @internal
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Jules Pietri <jules@heahprod.com>
 */
trait LocalizedRouteTrait
{
    /**
     * Creates one or many routes.
     *
     * @param string|array $path the path, or the localized paths of the route
     */
    final protected function createLocalizedRoute(RouteCollection $collection, string $name, string|array $path, string $namePrefix = '', ?array $prefixes = null): RouteCollection
    {
        $paths = [];

        $routes = new RouteCollection();

        if (\is_array($path)) {
            if (null === $prefixes) {
                $paths = $path;
            } elseif ($missing = array_diff_key($prefixes, $path)) {
                throw new \LogicException(\sprintf('Route "%s" is missing routes for locale(s) "%s".', $name, implode('", "', array_keys($missing))));
            } else {
                foreach ($path as $locale => $localePath) {
                    if (!isset($prefixes[$locale])) {
                        throw new \LogicException(\sprintf('Route "%s" with locale "%s" is missing a corresponding prefix in its parent collection.', $name, $locale));
                    }

                    $paths[$locale] = $prefixes[$locale].$localePath;
                }
            }
        } elseif (null !== $prefixes) {
            foreach ($prefixes as $locale => $prefix) {
                $paths[$locale] = $prefix.$path;
            }
        } else {
            $routes->add($namePrefix.$name, $route = $this->createRoute($path));
            $collection->add($namePrefix.$name, $route);

            return $routes;
        }

        foreach ($paths as $locale => $path) {
            $routes->add($name.'.'.$locale, $route = $this->createRoute($path));
            $collection->add($namePrefix.$name.'.'.$locale, $route);
            $route->setDefault('_locale', $locale);
            $route->setRequirement('_locale', preg_quote($locale));
            $route->setDefault('_canonical_route', $namePrefix.$name);
        }

        return $routes;
    }

    private function createRoute(string $path): Route
    {
        return new Route($path);
    }
}
