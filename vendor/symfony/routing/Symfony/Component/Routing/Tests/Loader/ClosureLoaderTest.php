<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Loader;

use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ClosureLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $loader = new ClosureLoader();

        $closure = function () {};

        $this->assertTrue($loader->supports($closure), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');

        $this->assertTrue($loader->supports($closure, 'closure'), '->supports() checks the resource type if specified');
        $this->assertFalse($loader->supports($closure, 'foo'), '->supports() checks the resource type if specified');
    }

    public function testLoad()
    {
        $loader = new ClosureLoader();

        $route = new Route('/');
        $routes = $loader->load(function () use ($route) {
            $routes = new RouteCollection();

            $routes->add('foo', $route);

            return $routes;
        });

        $this->assertEquals($route, $routes->get('foo'), '->load() loads a \Closure resource');
    }
}
