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

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\ApacheUrlMatcher;

class ApacheUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $server;

    protected function setUp()
    {
        $this->server = $_SERVER;
    }

    protected function tearDown()
    {
        $_SERVER = $this->server;
    }

    /**
     * @dataProvider getMatchData
     */
    public function testMatch($name, $pathinfo, $server, $expect)
    {
        $collection = new RouteCollection();
        $context = new RequestContext();
        $matcher = new ApacheUrlMatcher($collection, $context);

        $_SERVER = $server;

        $result = $matcher->match($pathinfo);
        $this->assertSame(var_export($expect, true), var_export($result, true));
    }

    public function getMatchData()
    {
        return array(
            array(
                'Simple route',
                '/hello/world',
                array(
                    '_ROUTING_route' => 'hello',
                    '_ROUTING_param__controller' => 'AcmeBundle:Default:index',
                    '_ROUTING_param_name' => 'world',
                ),
                array(
                    '_controller' => 'AcmeBundle:Default:index',
                    'name' => 'world',
                    '_route' => 'hello',
                ),
            ),
            array(
                'Route with params and defaults',
                '/hello/hugo',
                array(
                    '_ROUTING_route' => 'hello',
                    '_ROUTING_param__controller' => 'AcmeBundle:Default:index',
                    '_ROUTING_param_name' => 'hugo',
                    '_ROUTING_default_name' => 'world',
                ),
                array(
                    'name' => 'hugo',
                    '_controller' => 'AcmeBundle:Default:index',
                    '_route' => 'hello',
                ),
            ),
            array(
                'Route with defaults only',
                '/hello',
                array(
                    '_ROUTING_route' => 'hello',
                    '_ROUTING_param__controller' => 'AcmeBundle:Default:index',
                    '_ROUTING_default_name' => 'world',
                ),
                array(
                    'name' => 'world',
                    '_controller' => 'AcmeBundle:Default:index',
                    '_route' => 'hello',
                ),
            ),
            array(
                'Redirect with many ignored attributes',
                '/legacy/{cat1}/{cat2}/{id}.html',
                array(
                    '_ROUTING_route' => 'product_view',
                    '_ROUTING_param__controller' => 'FrameworkBundle:Redirect:redirect',
                    '_ROUTING_default_ignoreAttributes[0]' => 'attr_a',
                    '_ROUTING_default_ignoreAttributes[1]' => 'attr_b',
                ),
                array(
                    'ignoreAttributes' => array('attr_a', 'attr_b'),
                    '_controller' => 'FrameworkBundle:Redirect:redirect',
                    '_route' => 'product_view',
                ),
            ),
            array(
                'REDIRECT_ envs',
                '/hello/world',
                array(
                    'REDIRECT__ROUTING_route' => 'hello',
                    'REDIRECT__ROUTING_param__controller' => 'AcmeBundle:Default:index',
                    'REDIRECT__ROUTING_param_name' => 'world',
                ),
                array(
                    '_controller' => 'AcmeBundle:Default:index',
                    'name' => 'world',
                    '_route' => 'hello',
                ),
            ),
            array(
                'REDIRECT_REDIRECT_ envs',
                '/hello/world',
                array(
                    'REDIRECT_REDIRECT__ROUTING_route' => 'hello',
                    'REDIRECT_REDIRECT__ROUTING_param__controller' => 'AcmeBundle:Default:index',
                    'REDIRECT_REDIRECT__ROUTING_param_name' => 'world',
                ),
                array(
                    '_controller' => 'AcmeBundle:Default:index',
                    'name' => 'world',
                    '_route' => 'hello',
                ),
            ),
            array(
                'REDIRECT_REDIRECT_ envs',
                '/hello/world',
                array(
                    'REDIRECT_REDIRECT__ROUTING_route' => 'hello',
                    'REDIRECT_REDIRECT__ROUTING_param__controller' => 'AcmeBundle:Default:index',
                    'REDIRECT_REDIRECT__ROUTING_param_name' => 'world',
                ),
                array(
                    '_controller' => 'AcmeBundle:Default:index',
                    'name' => 'world',
                    '_route' => 'hello',
                ),
            ),
        );
    }
}
