<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DataCollector;

use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RequestDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testCollect(Request $request, Response $response)
    {
        $c = new RequestDataCollector();

        $c->collect($request, $response);

        $this->assertSame('request', $c->getName());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\HeaderBag', $c->getRequestHeaders());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestServer());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestCookies());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestAttributes());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestRequest());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $c->getRequestQuery());
        $this->assertSame('html', $c->getFormat());
        $this->assertSame('foobar', $c->getRoute());
        $this->assertSame(array('name' => 'foo'), $c->getRouteParams());
        $this->assertSame(array(), $c->getSessionAttributes());
        $this->assertSame('en', $c->getLocale());

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\HeaderBag', $c->getResponseHeaders());
        $this->assertSame('OK', $c->getStatusText());
        $this->assertSame(200, $c->getStatusCode());
        $this->assertSame('application/json', $c->getContentType());
    }

    /**
     * Test various types of controller callables.
     *
     * @dataProvider provider
     */
    public function testControllerInspection(Request $request, Response $response)
    {
        // make sure we always match the line number
        $r1 = new \ReflectionMethod($this, 'testControllerInspection');
        $r2 = new \ReflectionMethod($this, 'staticControllerMethod');
        // test name, callable, expected
        $controllerTests = array(
            array(
                '"Regular" callable',
                array($this, 'testControllerInspection'),
                array(
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'testControllerInspection',
                    'file' => __FILE__,
                    'line' => $r1->getStartLine(),
                ),
            ),

            array(
                'Closure',
                function () { return 'foo'; },
                array(
                    'class' => __NAMESPACE__.'\{closure}',
                    'method' => null,
                    'file' => __FILE__,
                    'line' => __LINE__ - 5,
                ),
            ),

            array(
                'Static callback as string',
                'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest::staticControllerMethod',
                'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest::staticControllerMethod',
            ),

            array(
                'Static callable with instance',
                array($this, 'staticControllerMethod'),
                array(
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'staticControllerMethod',
                    'file' => __FILE__,
                    'line' => $r2->getStartLine(),
                ),
            ),

            array(
                'Static callable with class name',
                array('Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest', 'staticControllerMethod'),
                array(
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'staticControllerMethod',
                    'file' => __FILE__,
                    'line' => $r2->getStartLine(),
                ),
            ),

            array(
                'Callable with instance depending on __call()',
                array($this, 'magicMethod'),
                array(
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'magicMethod',
                    'file' => 'n/a',
                    'line' => 'n/a',
                ),
            ),

            array(
                'Callable with class name depending on __callStatic()',
                array('Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest', 'magicMethod'),
                array(
                    'class' => 'Symfony\Component\HttpKernel\Tests\DataCollector\RequestDataCollectorTest',
                    'method' => 'magicMethod',
                    'file' => 'n/a',
                    'line' => 'n/a',
                ),
            ),
        );

        $c = new RequestDataCollector();

        foreach ($controllerTests as $controllerTest) {
            $this->injectController($c, $controllerTest[1], $request);
            $c->collect($request, $response);
            $this->assertSame($controllerTest[2], $c->getController(), sprintf('Testing: %s', $controllerTest[0]));
        }
    }

    public function provider()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            return array(array(null, null));
        }

        $request = Request::create('http://test.com/foo?bar=baz');
        $request->attributes->set('foo', 'bar');
        $request->attributes->set('_route', 'foobar');
        $request->attributes->set('_route_params', array('name' => 'foo'));

        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->setCookie(new Cookie('foo','bar',1,'/foo','localhost',true,true));
        $response->headers->setCookie(new Cookie('bar','foo',new \DateTime('@946684800')));
        $response->headers->setCookie(new Cookie('bazz','foo','2000-12-12'));

        return array(
            array($request, $response),
        );
    }

    /**
     * Inject the given controller callable into the data collector.
     */
    protected function injectController($collector, $controller, $request)
    {
        $resolver = $this->getMock('Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface');
        $httpKernel = new HttpKernel(new EventDispatcher(), $resolver);
        $event = new FilterControllerEvent($httpKernel, $controller, $request, HttpKernelInterface::MASTER_REQUEST);
        $collector->onKernelController($event);
    }

    /**
     * Dummy method used as controller callable
     */
    public static function staticControllerMethod()
    {
        throw new \LogicException('Unexpected method call');
    }

    /**
     * Magic method to allow non existing methods to be called and delegated.
     */
    public function __call($method, $args)
    {
        throw new \LogicException('Unexpected method call');
    }

    /**
     * Magic method to allow non existing methods to be called and delegated.
     */
    public static function __callStatic($method, $args)
    {
        throw new \LogicException('Unexpected method call');
    }
}
