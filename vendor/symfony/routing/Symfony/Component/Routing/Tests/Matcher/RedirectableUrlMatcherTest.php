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

class RedirectableUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testRedirectWhenNoSlash()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo/'));

        $matcher = $this->getMockForAbstractClass('Symfony\Component\Routing\Matcher\RedirectableUrlMatcher', array($coll, new RequestContext()));
        $matcher->expects($this->once())->method('redirect');
        $matcher->match('/foo');
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testRedirectWhenNoSlashForNonSafeMethod()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo/'));

        $context = new RequestContext();
        $context->setMethod('POST');
        $matcher = $this->getMockForAbstractClass('Symfony\Component\Routing\Matcher\RedirectableUrlMatcher', array($coll, $context));
        $matcher->match('/foo');
    }

    public function testSchemeRedirect()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo', array(), array('_scheme' => 'https')));

        $matcher = $this->getMockForAbstractClass('Symfony\Component\Routing\Matcher\RedirectableUrlMatcher', array($coll, new RequestContext()));
        $matcher
            ->expects($this->once())
            ->method('redirect')
            ->with('/foo', 'foo', 'https')
            ->will($this->returnValue(array('_route' => 'foo')))
        ;
        $matcher->match('/foo');
    }
}
