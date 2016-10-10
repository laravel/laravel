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

use Symfony\Component\HttpFoundation\HeaderBag;

class HeaderBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\Component\HttpFoundation\HeaderBag::__construct
     */
    public function testConstructor()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $this->assertTrue($bag->has('foo'));
    }

    public function testToStringNull()
    {
        $bag = new HeaderBag();
        $this->assertEquals('', $bag->__toString());
    }

    public function testToStringNotNull()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $this->assertEquals("Foo: bar\r\n", $bag->__toString());
    }

    public function testKeys()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $keys = $bag->keys();
        $this->assertEquals("foo", $keys[0]);
    }

    public function testGetDate()
    {
        $bag = new HeaderBag(array('foo' => 'Tue, 4 Sep 2012 20:00:00 +0200'));
        $headerDate = $bag->getDate('foo');
        $this->assertInstanceOf('DateTime', $headerDate);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetDateException()
    {
        $bag = new HeaderBag(array('foo' => 'Tue'));
        $headerDate = $bag->getDate('foo');
    }

    public function testGetCacheControlHeader()
    {
        $bag = new HeaderBag();
        $bag->addCacheControlDirective('public', '#a');
        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertEquals('#a', $bag->getCacheControlDirective('public'));
    }

    /**
     * @covers Symfony\Component\HttpFoundation\HeaderBag::all
     */
    public function testAll()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => array('bar')), $bag->all(), '->all() gets all the input');

        $bag = new HeaderBag(array('FOO' => 'BAR'));
        $this->assertEquals(array('foo' => array('BAR')), $bag->all(), '->all() gets all the input key are lower case');
    }

    /**
     * @covers Symfony\Component\HttpFoundation\HeaderBag::replace
     */
    public function testReplace()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));

        $bag->replace(array('NOPE' => 'BAR'));
        $this->assertEquals(array('nope' => array('BAR')), $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    /**
     * @covers Symfony\Component\HttpFoundation\HeaderBag::get
     */
    public function testGet()
    {
        $bag = new HeaderBag(array('foo' => 'bar', 'fuzz' => 'bizz'));
        $this->assertEquals( 'bar', $bag->get('foo'), '->get return current value');
        $this->assertEquals( 'bar', $bag->get('FoO'), '->get key in case insensitive');
        $this->assertEquals( array('bar'), $bag->get('foo', 'nope', false), '->get return the value as array');

        // defaults
        $this->assertNull($bag->get('none'), '->get unknown values returns null');
        $this->assertEquals( 'default', $bag->get('none', 'default'), '->get unknown values returns default');
        $this->assertEquals( array('default'), $bag->get('none', 'default', false), '->get unknown values returns default as array');

        $bag->set('foo', 'bor', false);
        $this->assertEquals( 'bar', $bag->get('foo'), '->get return first value');
        $this->assertEquals( array('bar', 'bor'), $bag->get('foo', 'nope', false), '->get return all values as array');
    }

    public function testSetAssociativeArray()
    {
        $bag = new HeaderBag();
        $bag->set('foo', array('bad-assoc-index' => 'value'));
        $this->assertSame('value', $bag->get('foo'));
        $this->assertEquals(array('value'), $bag->get('foo', 'nope', false), 'assoc indices of multi-valued headers are ignored');
    }

    /**
     * @covers Symfony\Component\HttpFoundation\HeaderBag::contains
     */
    public function testContains()
    {
        $bag = new HeaderBag(array('foo' => 'bar', 'fuzz' => 'bizz'));
        $this->assertTrue(  $bag->contains('foo', 'bar'), '->contains first value');
        $this->assertTrue(  $bag->contains('fuzz', 'bizz'), '->contains second value');
        $this->assertFalse(  $bag->contains('nope', 'nope'), '->contains unknown value');
        $this->assertFalse(  $bag->contains('foo', 'nope'), '->contains unknown value');

        // Multiple values
        $bag->set('foo', 'bor', false);
        $this->assertTrue(  $bag->contains('foo', 'bar'), '->contains first value');
        $this->assertTrue(  $bag->contains('foo', 'bor'), '->contains second value');
        $this->assertFalse(  $bag->contains('foo', 'nope'), '->contains unknown value');
    }

    public function testCacheControlDirectiveAccessors()
    {
        $bag = new HeaderBag();
        $bag->addCacheControlDirective('public');

        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertTrue($bag->getCacheControlDirective('public'));
        $this->assertEquals('public', $bag->get('cache-control'));

        $bag->addCacheControlDirective('max-age', 10);
        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertEquals(10, $bag->getCacheControlDirective('max-age'));
        $this->assertEquals('max-age=10, public', $bag->get('cache-control'));

        $bag->removeCacheControlDirective('max-age');
        $this->assertFalse($bag->hasCacheControlDirective('max-age'));
    }

    public function testCacheControlDirectiveParsing()
    {
        $bag = new HeaderBag(array('cache-control' => 'public, max-age=10'));
        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertTrue($bag->getCacheControlDirective('public'));

        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertEquals(10, $bag->getCacheControlDirective('max-age'));

        $bag->addCacheControlDirective('s-maxage', 100);
        $this->assertEquals('max-age=10, public, s-maxage=100', $bag->get('cache-control'));
    }

    public function testCacheControlDirectiveParsingQuotedZero()
    {
        $bag = new HeaderBag(array('cache-control' => 'max-age="0"'));
        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertEquals(0, $bag->getCacheControlDirective('max-age'));
    }

    public function testCacheControlDirectiveOverrideWithReplace()
    {
        $bag = new HeaderBag(array('cache-control' => 'private, max-age=100'));
        $bag->replace(array('cache-control' => 'public, max-age=10'));
        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertTrue($bag->getCacheControlDirective('public'));

        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertEquals(10, $bag->getCacheControlDirective('max-age'));
    }

    /**
     * @covers Symfony\Component\HttpFoundation\HeaderBag::getIterator
     */
    public function testGetIterator()
    {
        $headers   = array('foo' => 'bar', 'hello' => 'world', 'third' => 'charm');
        $headerBag = new HeaderBag($headers);

        $i = 0;
        foreach ($headerBag as $key => $val) {
            $i++;
            $this->assertEquals(array($headers[$key]), $val);
        }

        $this->assertEquals(count($headers), $i);
    }

    /**
     * @covers Symfony\Component\HttpFoundation\HeaderBag::count
     */
    public function testCount()
    {
        $headers   = array('foo' => 'bar', 'HELLO' => 'WORLD');
        $headerBag = new HeaderBag($headers);

        $this->assertEquals(count($headers), count($headerBag));
    }
}
