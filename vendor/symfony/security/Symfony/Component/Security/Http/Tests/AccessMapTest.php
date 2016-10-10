<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests;

use Symfony\Component\Security\Http\AccessMap;

class AccessMapTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsFirstMatchedPattern()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $requestMatcher1 = $this->getRequestMatcher($request, false);
        $requestMatcher2 = $this->getRequestMatcher($request, true);

        $map = new AccessMap();
        $map->add($requestMatcher1, array('ROLE_ADMIN'), 'http');
        $map->add($requestMatcher2, array('ROLE_USER'), 'https');

        $this->assertSame(array(array('ROLE_USER'), 'https'), $map->getPatterns($request));
    }

    public function testReturnsEmptyPatternIfNoneMatched()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $requestMatcher = $this->getRequestMatcher($request, false);

        $map = new AccessMap();
        $map->add($requestMatcher, array('ROLE_USER'), 'https');

        $this->assertSame(array(null, null), $map->getPatterns($request));
    }

    private function getRequestMatcher($request, $matches)
    {
        $requestMatcher = $this->getMock('Symfony\Component\HttpFoundation\RequestMatcherInterface');
        $requestMatcher->expects($this->once())
            ->method('matches')->with($request)
            ->will($this->returnValue($matches));

        return $requestMatcher;
    }
}
