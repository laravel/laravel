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

class ScalarFormatterTest extends \PHPUnit_Framework_TestCase
{
    private $formatter;

    public function setUp()
    {
        $this->formatter = new ScalarFormatter();
    }

    public function buildTrace(\Exception $e)
    {
        $data = array();
        $trace = $e->getTrace();
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $data[] = $frame['file'].':'.$frame['line'];
            } else {
                $data[] = json_encode($frame);
            }
        }

        return $data;
    }

    public function encodeJson($data)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return json_encode($data);
    }

    public function testFormat()
    {
        $exception = new \Exception('foo');
        $formatted = $this->formatter->format(array(
            'foo' => 'string',
            'bar' => 1,
            'baz' => false,
            'bam' => array(1, 2, 3),
            'bat' => array('foo' => 'bar'),
            'bap' => \DateTime::createFromFormat(\DateTime::ISO8601, '1970-01-01T00:00:00+0000'),
            'ban' => $exception,
        ));

        $this->assertSame(array(
            'foo' => 'string',
            'bar' => 1,
            'baz' => false,
            'bam' => $this->encodeJson(array(1, 2, 3)),
            'bat' => $this->encodeJson(array('foo' => 'bar')),
            'bap' => '1970-01-01 00:00:00',
            'ban' => $this->encodeJson(array(
                'class'   => get_class($exception),
                'message' => $exception->getMessage(),
                'code'    => $exception->getCode(),
                'file'    => $exception->getFile() . ':' . $exception->getLine(),
                'trace'   => $this->buildTrace($exception),
            )),
        ), $formatted);
    }

    public function testFormatWithErrorContext()
    {
        $context = array('file' => 'foo', 'line' => 1);
        $formatted = $this->formatter->format(array(
            'context' => $context,
        ));

        $this->assertSame(array(
            'context' => $this->encodeJson($context),
        ), $formatted);
    }

    public function testFormatWithExceptionContext()
    {
        $exception = new \Exception('foo');
        $formatted = $this->formatter->format(array(
            'context' => array(
                'exception' => $exception,
            ),
        ));

        $this->assertSame(array(
            'context' => $this->encodeJson(array(
                'exception' => array(
                    'class'   => get_class($exception),
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode(),
                    'file'    => $exception->getFile() . ':' . $exception->getLine(),
                    'trace'   => $this->buildTrace($exception),
                ),
            )),
        ), $formatted);
    }
}
