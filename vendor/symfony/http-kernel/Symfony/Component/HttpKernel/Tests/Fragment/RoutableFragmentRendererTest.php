<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Fragment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class RoutableFragmentRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getGenerateFragmentUriData
     */
    public function testGenerateFragmentUri($uri, $controller)
    {
        $this->assertEquals($uri, $this->callGenerateFragmentUriMethod($controller, Request::create('/')));
    }

    /**
     * @dataProvider getGenerateFragmentUriData
     */
    public function testGenerateAbsoluteFragmentUri($uri, $controller)
    {
        $this->assertEquals('http://localhost'.$uri, $this->callGenerateFragmentUriMethod($controller, Request::create('/'), true));
    }

    public function getGenerateFragmentUriData()
    {
        return array(
            array('/_fragment?_path=_format%3Dhtml%26_locale%3Den%26_controller%3Dcontroller', new ControllerReference('controller', array(), array())),
            array('/_fragment?_path=_format%3Dxml%26_locale%3Den%26_controller%3Dcontroller', new ControllerReference('controller', array('_format' => 'xml'), array())),
            array('/_fragment?_path=foo%3Dfoo%26_format%3Djson%26_locale%3Den%26_controller%3Dcontroller', new ControllerReference('controller', array('foo' => 'foo', '_format' => 'json'), array())),
            array('/_fragment?bar=bar&_path=foo%3Dfoo%26_format%3Dhtml%26_locale%3Den%26_controller%3Dcontroller', new ControllerReference('controller', array('foo' => 'foo'), array('bar' => 'bar'))),
            array('/_fragment?foo=foo&_path=_format%3Dhtml%26_locale%3Den%26_controller%3Dcontroller', new ControllerReference('controller', array(), array('foo' => 'foo'))),
            array('/_fragment?_path=foo%255B0%255D%3Dfoo%26foo%255B1%255D%3Dbar%26_format%3Dhtml%26_locale%3Den%26_controller%3Dcontroller', new ControllerReference('controller', array('foo' => array('foo', 'bar')), array())),
        );
    }

    public function testGenerateFragmentUriWithARequest()
    {
        $request = Request::create('/');
        $request->attributes->set('_format', 'json');
        $request->setLocale('fr');
        $controller = new ControllerReference('controller', array(), array());

        $this->assertEquals('/_fragment?_path=_format%3Djson%26_locale%3Dfr%26_controller%3Dcontroller', $this->callGenerateFragmentUriMethod($controller, $request));
    }

    /**
     * @expectedException LogicException
     * @dataProvider      getGenerateFragmentUriDataWithNonScalar
     */
    public function testGenerateFragmentUriWithNonScalar($controller)
    {
        $this->callGenerateFragmentUriMethod($controller, Request::create('/'));
    }

    public function getGenerateFragmentUriDataWithNonScalar()
    {
        return array(
            array(new ControllerReference('controller', array('foo' => new Foo(), 'bar' => 'bar'), array())),
            array(new ControllerReference('controller', array('foo' => array('foo' => 'foo'), 'bar' => array('bar' => new Foo())), array())),
        );
    }

    private function callGenerateFragmentUriMethod(ControllerReference $reference, Request $request, $absolute = false)
    {
        $renderer = $this->getMockForAbstractClass('Symfony\Component\HttpKernel\Fragment\RoutableFragmentRenderer');
        $r = new \ReflectionObject($renderer);
        $m = $r->getMethod('generateFragmentUri');
        $m->setAccessible(true);

        return $m->invoke($renderer, $reference, $request, $absolute);
    }
}

class Foo
{
    public $foo;

    public function getFoo()
    {
        return $this->foo;
    }
}
