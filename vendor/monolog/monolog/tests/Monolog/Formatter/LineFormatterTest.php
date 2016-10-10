<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

/**
 * @covers Monolog\Formatter\LineFormatter
 */
class LineFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testDefFormatWithString()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'WARNING',
            'channel' => 'log',
            'context' => array(),
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => array(),
        ));
        $this->assertEquals('['.date('Y-m-d').'] log.WARNING: foo [] []'."\n", $message);
    }

    public function testDefFormatWithArrayContext()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => array(),
            'context' => array(
                'foo' => 'bar',
                'baz' => 'qux',
                'bool' => false,
                'null' => null,
            ),
        ));
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: foo {"foo":"bar","baz":"qux","bool":false,"null":null} []'."\n", $message);
    }

    public function testDefFormatExtras()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new \DateTime,
            'extra' => array('ip' => '127.0.0.1'),
            'message' => 'log',
        ));
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log [] {"ip":"127.0.0.1"}'."\n", $message);
    }

    public function testFormatExtras()
    {
        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra.file% %extra%\n", 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new \DateTime,
            'extra' => array('ip' => '127.0.0.1', 'file' => 'test'),
            'message' => 'log',
        ));
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log [] test {"ip":"127.0.0.1"}'."\n", $message);
    }

    public function testContextAndExtraOptionallyNotShownIfEmpty()
    {
        $formatter = new LineFormatter(null, 'Y-m-d', false, true);
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new \DateTime,
            'extra' => array(),
            'message' => 'log',
        ));
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log  '."\n", $message);
    }

    public function testContextAndExtraReplacement()
    {
        $formatter = new LineFormatter('%context.foo% => %extra.foo%');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('foo' => 'bar'),
            'datetime' => new \DateTime,
            'extra' => array('foo' => 'xbar'),
            'message' => 'log',
        ));
        $this->assertEquals('bar => xbar', $message);
    }

    public function testDefFormatWithObject()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new \DateTime,
            'extra' => array('foo' => new TestFoo, 'bar' => new TestBar, 'baz' => array(), 'res' => fopen('php://memory', 'rb')),
            'message' => 'foobar',
        ));

        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: foobar [] {"foo":"[object] (Monolog\\\\Formatter\\\\TestFoo: {\\"foo\\":\\"foo\\"})","bar":"[object] (Monolog\\\\Formatter\\\\TestBar: bar)","baz":[],"res":"[resource] (stream)"}'."\n", $message);
    }

    public function testDefFormatWithException()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'CRITICAL',
            'channel' => 'core',
            'context' => array('exception' => new \RuntimeException('Foo')),
            'datetime' => new \DateTime,
            'extra' => array(),
            'message' => 'foobar',
        ));

        $path = str_replace('\\/', '/', json_encode(__FILE__));

        $this->assertEquals('['.date('Y-m-d').'] core.CRITICAL: foobar {"exception":"[object] (RuntimeException(code: 0): Foo at '.substr($path, 1, -1).':'.(__LINE__ - 8).')"} []'."\n", $message);
    }

    public function testDefFormatWithPreviousException()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $previous = new \LogicException('Wut?');
        $message = $formatter->format(array(
            'level_name' => 'CRITICAL',
            'channel' => 'core',
            'context' => array('exception' => new \RuntimeException('Foo', 0, $previous)),
            'datetime' => new \DateTime,
            'extra' => array(),
            'message' => 'foobar',
        ));

        $path = str_replace('\\/', '/', json_encode(__FILE__));

        $this->assertEquals('['.date('Y-m-d').'] core.CRITICAL: foobar {"exception":"[object] (RuntimeException(code: 0): Foo at '.substr($path, 1, -1).':'.(__LINE__ - 8).', LogicException(code: 0): Wut? at '.substr($path, 1, -1).':'.(__LINE__ - 12).')"} []'."\n", $message);
    }

    public function testBatchFormat()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->formatBatch(array(
            array(
                'level_name' => 'CRITICAL',
                'channel' => 'test',
                'message' => 'bar',
                'context' => array(),
                'datetime' => new \DateTime,
                'extra' => array(),
            ),
            array(
                'level_name' => 'WARNING',
                'channel' => 'log',
                'message' => 'foo',
                'context' => array(),
                'datetime' => new \DateTime,
                'extra' => array(),
            ),
        ));
        $this->assertEquals('['.date('Y-m-d').'] test.CRITICAL: bar [] []'."\n".'['.date('Y-m-d').'] log.WARNING: foo [] []'."\n", $message);
    }

    public function testFormatShouldStripInlineLineBreaks()
    {
        $formatter = new LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(
            array(
                'message' => "foo\nbar",
                'context' => array(),
                'extra' => array(),
            )
        );

        $this->assertRegExp('/foo bar/', $message);
    }

    public function testFormatShouldNotStripInlineLineBreaksWhenFlagIsSet()
    {
        $formatter = new LineFormatter(null, 'Y-m-d', true);
        $message = $formatter->format(
            array(
                'message' => "foo\nbar",
                'context' => array(),
                'extra' => array(),
            )
        );

        $this->assertRegExp('/foo\nbar/', $message);
    }
}

class TestFoo
{
    public $foo = 'foo';
}

class TestBar
{
    public function __toString()
    {
        return 'bar';
    }
}
