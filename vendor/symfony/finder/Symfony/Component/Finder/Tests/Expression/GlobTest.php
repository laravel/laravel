<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\Expression;

use Symfony\Component\Finder\Expression\Expression;

class GlobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getToRegexData
     */
    public function testGlobToRegex($glob, $match, $noMatch)
    {
        foreach ($match as $m) {
            $this->assertRegExp(Expression::create($glob)->getRegex()->render(), $m, '::toRegex() converts a glob to a regexp');
        }

        foreach ($noMatch as $m) {
            $this->assertNotRegExp(Expression::create($glob)->getRegex()->render(), $m, '::toRegex() converts a glob to a regexp');
        }
    }

    public function getToRegexData()
    {
        return array(
            array('', array(''), array('f', '/')),
            array('*', array('foo'), array('foo/', '/foo')),
            array('foo.*', array('foo.php', 'foo.a', 'foo.'), array('fooo.php', 'foo.php/foo')),
            array('fo?', array('foo', 'fot'), array('fooo', 'ffoo', 'fo/')),
            array('fo{o,t}', array('foo', 'fot'), array('fob', 'fo/')),
            array('foo(bar|foo)', array('foo(bar|foo)'), array('foobar', 'foofoo')),
            array('foo,bar', array('foo,bar'), array('foo', 'bar')),
            array('fo{o,\\,}', array('foo', 'fo,'), array()),
            array('fo{o,\\\\}', array('foo', 'fo\\'), array()),
            array('/foo', array('/foo'), array('foo')),
        );
    }
}
