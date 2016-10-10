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

use Monolog\Logger;

/**
 * @author Florian Plattner <me@florianplattner.de>
 */
class MongoDBFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('MongoDate')) {
            $this->markTestSkipped('mongo extension not installed');
        }
    }

    public function constructArgumentProvider()
    {
        return array(
            array(1, true, 1, true),
            array(0, false, 0, false),
        );
    }

    /**
     * @param $traceDepth
     * @param $traceAsString
     * @param $expectedTraceDepth
     * @param $expectedTraceAsString
     *
     * @dataProvider constructArgumentProvider
     */
    public function testConstruct($traceDepth, $traceAsString, $expectedTraceDepth, $expectedTraceAsString)
    {
        $formatter = new MongoDBFormatter($traceDepth, $traceAsString);

        $reflTrace = new \ReflectionProperty($formatter, 'exceptionTraceAsString');
        $reflTrace->setAccessible(true);
        $this->assertEquals($expectedTraceAsString, $reflTrace->getValue($formatter));

        $reflDepth = new\ReflectionProperty($formatter, 'maxNestingLevel');
        $reflDepth->setAccessible(true);
        $this->assertEquals($expectedTraceDepth, $reflDepth->getValue($formatter));
    }

    public function testSimpleFormat()
    {
        $record = array(
            'message' => 'some log message',
            'context' => array(),
            'level' => Logger::WARNING,
            'level_name' => Logger::getLevelName(Logger::WARNING),
            'channel' => 'test',
            'datetime' => new \DateTime('2014-02-01 00:00:00'),
            'extra' => array(),
        );

        $formatter = new MongoDBFormatter();
        $formattedRecord = $formatter->format($record);

        $this->assertCount(7, $formattedRecord);
        $this->assertEquals('some log message', $formattedRecord['message']);
        $this->assertEquals(array(), $formattedRecord['context']);
        $this->assertEquals(Logger::WARNING, $formattedRecord['level']);
        $this->assertEquals(Logger::getLevelName(Logger::WARNING), $formattedRecord['level_name']);
        $this->assertEquals('test', $formattedRecord['channel']);
        $this->assertInstanceOf('\MongoDate', $formattedRecord['datetime']);
        $this->assertEquals('0.00000000 1391212800', $formattedRecord['datetime']->__toString());
        $this->assertEquals(array(), $formattedRecord['extra']);
    }

    public function testRecursiveFormat()
    {
        $someObject = new \stdClass();
        $someObject->foo = 'something';
        $someObject->bar = 'stuff';

        $record = array(
            'message' => 'some log message',
            'context' => array(
                'stuff' => new \DateTime('2014-02-01 02:31:33'),
                'some_object' => $someObject,
                'context_string' => 'some string',
                'context_int' => 123456,
                'except' => new \Exception('exception message', 987),
            ),
            'level' => Logger::WARNING,
            'level_name' => Logger::getLevelName(Logger::WARNING),
            'channel' => 'test',
            'datetime' => new \DateTime('2014-02-01 00:00:00'),
            'extra' => array(),
        );

        $formatter = new MongoDBFormatter();
        $formattedRecord = $formatter->format($record);

        $this->assertCount(5, $formattedRecord['context']);
        $this->assertInstanceOf('\MongoDate', $formattedRecord['context']['stuff']);
        $this->assertEquals('0.00000000 1391221893', $formattedRecord['context']['stuff']->__toString());
        $this->assertEquals(
            array(
                'foo' => 'something',
                'bar' => 'stuff',
                'class' => 'stdClass',
            ),
            $formattedRecord['context']['some_object']
        );
        $this->assertEquals('some string', $formattedRecord['context']['context_string']);
        $this->assertEquals(123456, $formattedRecord['context']['context_int']);

        $this->assertCount(5, $formattedRecord['context']['except']);
        $this->assertEquals('exception message', $formattedRecord['context']['except']['message']);
        $this->assertEquals(987, $formattedRecord['context']['except']['code']);
        $this->assertInternalType('string', $formattedRecord['context']['except']['file']);
        $this->assertInternalType('integer', $formattedRecord['context']['except']['code']);
        $this->assertInternalType('string', $formattedRecord['context']['except']['trace']);
        $this->assertEquals('Exception', $formattedRecord['context']['except']['class']);
    }

    public function testFormatDepthArray()
    {
        $record = array(
            'message' => 'some log message',
            'context' => array(
                'nest2' => array(
                    'property' => 'anything',
                    'nest3' => array(
                        'nest4' => 'value',
                        'property' => 'nothing',
                    ),
                ),
            ),
            'level' => Logger::WARNING,
            'level_name' => Logger::getLevelName(Logger::WARNING),
            'channel' => 'test',
            'datetime' => new \DateTime('2014-02-01 00:00:00'),
            'extra' => array(),
        );

        $formatter = new MongoDBFormatter(2);
        $formattedResult = $formatter->format($record);

        $this->assertEquals(
            array(
                'nest2' => array(
                    'property' => 'anything',
                    'nest3' => '[...]',
                ),
            ),
            $formattedResult['context']
        );
    }

    public function testFormatDepthArrayInfiniteNesting()
    {
        $record = array(
            'message' => 'some log message',
            'context' => array(
                'nest2' => array(
                    'property' => 'something',
                    'nest3' => array(
                        'property' => 'anything',
                        'nest4' => array(
                            'property' => 'nothing',
                        ),
                    ),
                ),
            ),
            'level' => Logger::WARNING,
            'level_name' => Logger::getLevelName(Logger::WARNING),
            'channel' => 'test',
            'datetime' => new \DateTime('2014-02-01 00:00:00'),
            'extra' => array(),
        );

        $formatter = new MongoDBFormatter(0);
        $formattedResult = $formatter->format($record);

        $this->assertEquals(
            array(
                'nest2' => array(
                    'property' => 'something',
                    'nest3' => array(
                        'property' => 'anything',
                        'nest4' => array(
                            'property' => 'nothing',
                        ),
                    ),
                ),
            ),
            $formattedResult['context']
        );
    }

    public function testFormatDepthObjects()
    {
        $someObject = new \stdClass();
        $someObject->property = 'anything';
        $someObject->nest3 = new \stdClass();
        $someObject->nest3->property = 'nothing';
        $someObject->nest3->nest4 = 'invisible';

        $record = array(
            'message' => 'some log message',
            'context' => array(
                'nest2' => $someObject,
            ),
            'level' => Logger::WARNING,
            'level_name' => Logger::getLevelName(Logger::WARNING),
            'channel' => 'test',
            'datetime' => new \DateTime('2014-02-01 00:00:00'),
            'extra' => array(),
        );

        $formatter = new MongoDBFormatter(2, true);
        $formattedResult = $formatter->format($record);

        $this->assertEquals(
            array(
                'nest2' => array(
                    'property' => 'anything',
                    'nest3' => '[...]',
                    'class' => 'stdClass',
                ),
            ),
            $formattedResult['context']
        );
    }

    public function testFormatDepthException()
    {
        $record = array(
            'message' => 'some log message',
            'context' => array(
                'nest2' => new \Exception('exception message', 987),
            ),
            'level' => Logger::WARNING,
            'level_name' => Logger::getLevelName(Logger::WARNING),
            'channel' => 'test',
            'datetime' => new \DateTime('2014-02-01 00:00:00'),
            'extra' => array(),
        );

        $formatter = new MongoDBFormatter(2, false);
        $formattedRecord = $formatter->format($record);

        $this->assertEquals('exception message', $formattedRecord['context']['nest2']['message']);
        $this->assertEquals(987, $formattedRecord['context']['nest2']['code']);
        $this->assertEquals('[...]', $formattedRecord['context']['nest2']['trace']);
    }
}
