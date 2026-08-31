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
 * ClosureLoader loads routes from a PHP closure.
 *
 * The Closure must return a RouteCollection instance.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ClosureLoader extends Loader
{
    /**
     * Loads a Closure.
     */
    public function load(mixed $closure, ?string $type = null): RouteCollection
    {
        return $closure($this->env);
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return $resource instanceof \Closure && (!$type || 'closure' === $type);
    }
}
