<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Annotation;

use Symfony\Component\Routing\Annotation\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \BadMethodCallException
     */
    public function testInvalidRouteParameter()
    {
        $route = new Route(array('foo' => 'bar'));
    }

    /**
     * @dataProvider getValidParameters
     */
    public function testRouteParameters($parameter, $value, $getter)
    {
        $route = new Route(array($parameter => $value));
        $this->assertEquals($route->$getter(), $value);
    }

    public function getValidParameters()
    {
        return array(
            array('value', '/Blog', 'getPattern'),
            array('value', '/Blog', 'getPath'),
            array('requirements', array('_method' => 'GET'), 'getRequirements'),
            array('options', array('compiler_class' => 'RouteCompiler'), 'getOptions'),
            array('name', 'blog_index', 'getName'),
            array('defaults', array('_controller' => 'MyBlogBundle:Blog:index'), 'getDefaults'),
            array('schemes', array('https'), 'getSchemes'),
            array('methods', array('GET', 'POST'), 'getMethods'),
            array('host', array('{locale}.example.com'), 'getHost'),
            array('condition', array('context.getMethod() == "GET"'), 'getCondition'),
        );
    }
}
