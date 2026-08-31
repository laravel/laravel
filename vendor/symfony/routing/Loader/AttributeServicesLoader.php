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

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads routes from a list of tagged classes by delegating to the attribute class loader.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class AttributeServicesLoader extends Loader
{
    /**
     * @param class-string[] $taggedClasses
     */
    public function __construct(
        private array $taggedClasses = [],
    ) {
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $collection = new RouteCollection();

        foreach ($this->taggedClasses as $class) {
            $collection->addCollection($this->import($class, 'attribute'));
        }

        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'routing.controllers' === $resource;
    }
}
