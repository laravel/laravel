<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests;

use Symfony\Component\CssSelector\CssSelector;

class CssSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCssToXPath()
    {
        $this->assertEquals('descendant-or-self::*', CssSelector::toXPath(''));
        $this->assertEquals('descendant-or-self::h1', CssSelector::toXPath('h1'));
        $this->assertEquals("descendant-or-self::h1[@id = 'foo']", CssSelector::toXPath('h1#foo'));
        $this->assertEquals("descendant-or-self::h1[@class and contains(concat(' ', normalize-space(@class), ' '), ' foo ')]", CssSelector::toXPath('h1.foo'));
        $this->assertEquals('descendant-or-self::foo:h1', CssSelector::toXPath('foo|h1'));
    }

    /** @dataProvider getCssToXPathWithoutPrefixTestData */
    public function testCssToXPathWithoutPrefix($css, $xpath)
    {
        $this->assertEquals($xpath, CssSelector::toXPath($css, ''), '->parse() parses an input string and returns a node');
    }

    public function testParseExceptions()
    {
        try {
            CssSelector::toXPath('h1:');
            $this->fail('->parse() throws an Exception if the css selector is not valid');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Symfony\Component\CssSelector\Exception\ParseException', $e, '->parse() throws an Exception if the css selector is not valid');
            $this->assertEquals("Expected identifier, but <eof at 3> found.", $e->getMessage(), '->parse() throws an Exception if the css selector is not valid');
        }
    }

    public function getCssToXPathWithoutPrefixTestData()
    {
        return array(
            array('h1', "h1"),
            array('foo|h1', "foo:h1"),
            array('h1, h2, h3', "h1 | h2 | h3"),
            array('h1:nth-child(3n+1)', "*/*[name() = 'h1' and (position() - 1 >= 0 and (position() - 1) mod 3 = 0)]"),
            array('h1 > p', "h1/p"),
            array('h1#foo', "h1[@id = 'foo']"),
            array('h1.foo', "h1[@class and contains(concat(' ', normalize-space(@class), ' '), ' foo ')]"),
            array('h1[class*="foo bar"]', "h1[@class and contains(@class, 'foo bar')]"),
            array('h1[foo|class*="foo bar"]', "h1[@foo:class and contains(@foo:class, 'foo bar')]"),
            array('h1[class]', "h1[@class]"),
            array('h1 .foo', "h1/descendant-or-self::*/*[@class and contains(concat(' ', normalize-space(@class), ' '), ' foo ')]"),
            array('h1 #foo', "h1/descendant-or-self::*/*[@id = 'foo']"),
            array('h1 [class*=foo]', "h1/descendant-or-self::*/*[@class and contains(@class, 'foo')]"),
            array('div>.foo', "div/*[@class and contains(concat(' ', normalize-space(@class), ' '), ' foo ')]"),
            array('div > .foo', "div/*[@class and contains(concat(' ', normalize-space(@class), ' '), ' foo ')]"),
        );
    }
}
