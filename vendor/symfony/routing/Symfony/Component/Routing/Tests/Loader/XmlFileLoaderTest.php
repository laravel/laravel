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
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Tests\Fixtures\CustomXmlFileLoader;

class XmlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $loader = new XmlFileLoader($this->getMock('Symfony\Component\Config\FileLocator'));

        $this->assertTrue($loader->supports('foo.xml'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');

        $this->assertTrue($loader->supports('foo.xml', 'xml'), '->supports() checks the resource type if specified');
        $this->assertFalse($loader->supports('foo.xml', 'foo'), '->supports() checks the resource type if specified');
    }

    public function testLoadWithRoute()
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $routeCollection = $loader->load('validpattern.xml');
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

    public function testLoadWithNamespacePrefix()
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $routeCollection = $loader->load('namespaceprefix.xml');

        $this->assertCount(1, $routeCollection->all(), 'One route is loaded');

        $route = $routeCollection->get('blog_show');
        $this->assertSame('/blog/{slug}', $route->getPath());
        $this->assertSame('{_locale}.example.com', $route->getHost());
        $this->assertSame('MyBundle:Blog:show', $route->getDefault('_controller'));
        $this->assertSame('\w+', $route->getRequirement('slug'));
        $this->assertSame('en|fr|de', $route->getRequirement('_locale'));
        $this->assertSame(null, $route->getDefault('slug'));
        $this->assertSame('RouteCompiler', $route->getOption('compiler_class'));
    }

    public function testLoadWithImport()
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $routeCollection = $loader->load('validresource.xml');
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

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getPathsToInvalidFiles
     */
    public function testLoadThrowsExceptionWithInvalidFile($filePath)
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $loader->load($filePath);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getPathsToInvalidFiles
     */
    public function testLoadThrowsExceptionWithInvalidFileEvenWithoutSchemaValidation($filePath)
    {
        $loader = new CustomXmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $loader->load($filePath);
    }

    public function getPathsToInvalidFiles()
    {
        return array(array('nonvalidnode.xml'), array('nonvalidroute.xml'), array('nonvalid.xml'), array('missing_id.xml'), array('missing_path.xml'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Document types are not allowed.
     */
    public function testDocTypeIsNotAllowed()
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $loader->load('withdoctype.xml');
    }

    public function testNullValues()
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures')));
        $routeCollection = $loader->load('null_values.xml');
        $route = $routeCollection->get('blog_show');

        $this->assertTrue($route->hasDefault('foo'));
        $this->assertNull($route->getDefault('foo'));
        $this->assertTrue($route->hasDefault('bar'));
        $this->assertNull($route->getDefault('bar'));
        $this->assertEquals('foo', $route->getDefault('foobar'));
        $this->assertEquals('bar', $route->getDefault('baz'));
    }
}
