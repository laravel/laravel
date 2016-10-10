<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\TestCase;

class DynamoDbHandlerTest extends TestCase
{
    private $client;

    public function setUp()
    {
        if (!class_exists('Aws\DynamoDb\DynamoDbClient')) {
            $this->markTestSkipped('aws/aws-sdk-php not installed');
        }

        $this->client = $this->getMockBuilder('Aws\DynamoDb\DynamoDbClient')
            ->setMethods(array('formatAttributes', '__call'))
            ->disableOriginalConstructor()->getMock();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('Monolog\Handler\DynamoDbHandler', new DynamoDbHandler($this->client, 'foo'));
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Monolog\Handler\HandlerInterface', new DynamoDbHandler($this->client, 'foo'));
    }

    public function testGetFormatter()
    {
        $handler = new DynamoDbHandler($this->client, 'foo');
        $this->assertInstanceOf('Monolog\Formatter\ScalarFormatter', $handler->getFormatter());
    }

    public function testHandle()
    {
        $record = $this->getRecord();
        $formatter = $this->getMock('Monolog\Formatter\FormatterInterface');
        $formatted = array('foo' => 1, 'bar' => 2);
        $handler = new DynamoDbHandler($this->client, 'foo');
        $handler->setFormatter($formatter);

        $formatter
             ->expects($this->once())
             ->method('format')
             ->with($record)
             ->will($this->returnValue($formatted));
        $this->client
             ->expects($this->once())
             ->method('formatAttributes')
             ->with($this->isType('array'))
             ->will($this->returnValue($formatted));
        $this->client
             ->expects($this->once())
             ->method('__call')
             ->with('putItem', array(array(
                 'TableName' => 'foo',
                 'Item' => $formatted,
             )));

        $handler->handle($record);
    }
}
