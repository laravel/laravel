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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\Resource\FileResource;

class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $loader = new YamlFileLoader($this->getMock('Symfony\Component\Config\FileLocator'));

        $this->assertTrue($loader->supports('foo.yml'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');

        $this->assertTrue($loader->supports('foo.yml', 'yaml'), '->supports() checks the resource type if specified');
        $this->assertFalse($loader->supports('foo.yml', 'foo'), '->supports() checks the resource type if specified');
    }

    public function testLoadDoesNothingIfEmpty()
    {
        $loader = new YamlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $collection = $loader->load('empty.yml');

        $this->assertEquals(array(), $collection->all());
        $this->assertEquals(array(new FileResource(realpath(__DIR__.'/../Fixtures/empty.yml'))), $collection->getResources());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getPathsToInvalidFiles
     */
    public function testLoadThrowsExceptionWithInvalidFile($filePath)
    {
        $loader = new YamlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $loader->load($filePath);
    }

    public function getPathsToInvalidFiles()
    {
        return array(array('nonvalid.yml'), array('nonvalid2.yml'), array('incomplete.yml'), array('nonvalidkeys.yml'), array('nonesense_resource_plus_path.yml'), array('nonesense_type_without_resource.yml'));
    }

    public function testLoadSpecialRouteName()
    {
        $loader = new YamlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $routeCollection = $loader->load('special_route_name.yml');
        $route = $routeCollection->get('#$péß^a|');

        $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
        $this->assertSame('/true', $route->getPath());
    }

    public function testLoadWithRoute()
    {
        $loader = new YamlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $routeCollection = $loader->load('validpattern.yml');
        $routes = $routeCollection->all();

        $this->assertCount(3, $routes, 'Three routes are loaded');
        $this->assertContainsOnly('Symfony\Component\Routing\Route', $routes);

        $identicalRoutes = array_slice($routes, 0, 2);

        foreach ($identicalRoutes as $route) {
            $this->assertSame('/blog/{slug}', $route->getPath());
            $this->assertSame('{locale}.example.com', $route->getHost());
            $this->assertSame('MyBundle:Blog:show', $route->getDefault('_controller'));
            $this->assertSame('\w+', $route->getRequirement('locale'));
            $this->assertSame('RouteCompiler', $route->getOption('compiler_class'));
            $this->assertEquals(array('GET', 'POST', 'PUT', 'OPTIONS'), $route->getMethods());
            $this->assertEquals(array('https'), $route->getSchemes());
            $this->assertEquals('context.getMethod() == "GET"', $route->getCondition());
        }
    }

    public function testLoadWithResource()
    {
        $loader = new YamlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $routeCollection = $loader->load('validresource.yml');
        $routes = $routeCollection->all();

        $this->assertCount(3, $routes, 'Three routes are loaded');
        $this->assertContainsOnly('Symfony\Component\Routing\Route', $routes);

        foreach ($routes as $route) {
            $this->assertSame('/{foo}/blog/{slug}', $route->getPath());
            $this->assertSame('123', $route->getDefault('foo'));
            $this->assertSame('\d+', $route->getRequirement('foo'));
            $this->assertSame('bar', $route->getOption('foo'));
            $this->assertSame('', $route->getHost());
            $this->assertSame('context.getMethod() == "POST"', $route->getCondition());
        }
    }
}
