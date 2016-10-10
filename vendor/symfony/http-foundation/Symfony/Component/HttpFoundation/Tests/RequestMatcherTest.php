<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;

class RequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testMethodFixtures
     */
    public function testMethod($requestMethod, $matcherMethod, $isMatch)
    {
        $matcher = new RequestMatcher();
        $matcher->matchMethod($matcherMethod);
        $request = Request::create('', $requestMethod);
        $this->assertSame($isMatch, $matcher->matches($request));

        $matcher = new RequestMatcher(null, null, $matcherMethod);
        $request = Request::create('', $requestMethod);
        $this->assertSame($isMatch, $matcher->matches($request));
    }

    public function testMethodFixtures()
    {
        return array(
            array('get', 'get', true),
            array('get', array('get', 'post'), true),
            array('get', 'post', false),
            array('get', 'GET', true),
            array('get', array('GET', 'POST'), true),
            array('get', 'POST', false),
        );
    }

    /**
     * @dataProvider testHostFixture
     */
    public function testHost($pattern, $isMatch)
    {
        $matcher = new RequestMatcher();
        $request = Request::create('', 'get', array(), array(), array(), array('HTTP_HOST' => 'foo.example.com'));

        $matcher->matchHost($pattern);
        $this->assertSame($isMatch, $matcher->matches($request));

        $matcher = new RequestMatcher(null, $pattern);
        $this->assertSame($isMatch, $matcher->matches($request));
    }

    public function testHostFixture()
    {
        return array(
            array('.*\.example\.com', true),
            array('\.example\.com$', true),
            array('^.*\.example\.com$', true),
            array('.*\.sensio\.com', false),
            array('.*\.example\.COM', true),
            array('\.example\.COM$', true),
            array('^.*\.example\.COM$', true),
            array('.*\.sensio\.COM', false),        );
    }

    public function testPath()
    {
        $matcher = new RequestMatcher();

        $request = Request::create('/admin/foo');

        $matcher->matchPath('/admin/.*');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchPath('/admin');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchPath('^/admin/.*$');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchMethod('/blog/.*');
        $this->assertFalse($matcher->matches($request));
    }

    public function testPathWithLocaleIsNotSupported()
    {
        $matcher = new RequestMatcher();
        $request = Request::create('/en/login');
        $request->setLocale('en');

        $matcher->matchPath('^/{_locale}/login$');
        $this->assertFalse($matcher->matches($request));
    }

    public function testPathWithEncodedCharacters()
    {
        $matcher = new RequestMatcher();
        $request = Request::create('/admin/fo%20o');
        $matcher->matchPath('^/admin/fo o*$');
        $this->assertTrue($matcher->matches($request));
    }

    public function testAttributes()
    {
        $matcher = new RequestMatcher();

        $request = Request::create('/admin/foo');
        $request->attributes->set('foo', 'foo_bar');

        $matcher->matchAttribute('foo', 'foo_.*');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchAttribute('foo', 'foo');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchAttribute('foo', '^foo_bar$');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchAttribute('foo', 'babar');
        $this->assertFalse($matcher->matches($request));
    }
}
