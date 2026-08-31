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

use Symfony\Component\Routing\Loader\Configurator\AliasConfigurator;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RouteConfigurator;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait AddTrait
{
    use LocalizedRouteTrait;

    protected RouteCollection $collection;
    protected string $name = '';
    protected ?array $prefixes = null;

    /**
     * Adds a route.
     *
     * @param string|array $path the path, or the localized paths of the route
     */
    public function add(string $name, string|array $path): RouteConfigurator
    {
        $parentConfigurator = $this instanceof CollectionConfigurator ? $this : ($this instanceof RouteConfigurator ? $this->parentConfigurator : null);
        $route = $this->createLocalizedRoute($this->collection, $name, $path, $this->name, $this->prefixes);

        return new RouteConfigurator($this->collection, $route, $this->name, $parentConfigurator, $this->prefixes);
    }

    public function alias(string $name, string $alias): AliasConfigurator
    {
        return new AliasConfigurator($this->collection->addAlias($name, $alias));
    }

    /**
     * Adds a route.
     *
     * @param string|array $path the path, or the localized paths of the route
     */
    public function __invoke(string $name, string|array $path): RouteConfigurator
    {
        return $this->add($name, $path);
    }
}
