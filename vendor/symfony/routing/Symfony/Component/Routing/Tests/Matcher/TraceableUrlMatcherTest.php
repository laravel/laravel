<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Matcher;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;

class TraceableUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo', array(), array('_method' => 'POST')));
        $coll->add('bar', new Route('/bar/{id}', array(), array('id' => '\d+')));
        $coll->add('bar1', new Route('/bar/{name}', array(), array('id' => '\w+', '_method' => 'POST')));
        $coll->add('bar2', new Route('/foo', array(), array(), array(), 'baz'));
        $coll->add('bar3', new Route('/foo1', array(), array(), array(), 'baz'));
        $coll->add('bar4', new Route('/foo2', array(), array(), array(), 'baz', array(), array(), 'context.getMethod() == "GET"'));

        $context = new RequestContext();
        $context->setHost('baz');

        $matcher = new TraceableUrlMatcher($coll, $context);
        $traces = $matcher->getTraces('/babar');
        $this->assertEquals(array(0, 0, 0, 0, 0, 0), $this->getLevels($traces));

        $traces = $matcher->getTraces('/foo');
        $this->assertEquals(array(1, 0, 0, 2), $this->getLevels($traces));

        $traces = $matcher->getTraces('/bar/12');
        $this->assertEquals(array(0, 2), $this->getLevels($traces));

        $traces = $matcher->getTraces('/bar/dd');
        $this->assertEquals(array(0, 1, 1, 0, 0, 0), $this->getLevels($traces));

        $traces = $matcher->getTraces('/foo1');
        $this->assertEquals(array(0, 0, 0, 0, 2), $this->getLevels($traces));

        $context->setMethod('POST');
        $traces = $matcher->getTraces('/foo');
        $this->assertEquals(array(2), $this->getLevels($traces));

        $traces = $matcher->getTraces('/bar/dd');
        $this->assertEquals(array(0, 1, 2), $this->getLevels($traces));

        $traces = $matcher->getTraces('/foo2');
        $this->assertEquals(array(0, 0, 0, 0, 0, 1), $this->getLevels($traces));
    }

    public function testMatchRouteOnMultipleHosts()
    {
        $routes = new RouteCollection();
        $routes->add('first', new Route(
            '/mypath/',
            array('_controller' => 'MainBundle:Info:first'),
            array(),
            array(),
            'some.example.com'
        ));

        $routes->add('second', new Route(
            '/mypath/',
            array('_controller' => 'MainBundle:Info:second'),
            array(),
            array(),
            'another.example.com'
        ));

        $context = new RequestContext();
        $context->setHost('baz');

        $matcher = new TraceableUrlMatcher($routes, $context);

        $traces = $matcher->getTraces('/mypath/');
        $this->assertEquals(
            array(TraceableUrlMatcher::ROUTE_ALMOST_MATCHES, TraceableUrlMatcher::ROUTE_ALMOST_MATCHES),
            $this->getLevels($traces)
        );
    }

    public function getLevels($traces)
    {
        $levels = array();
        foreach ($traces as $trace) {
            $levels[] = $trace['level'];
        }

        return $levels;
    }
}
