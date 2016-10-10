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
use Monolog\Logger;

class MongoDBHandlerTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorShouldThrowExceptionForInvalidMongo()
    {
        new MongoDBHandler(new \stdClass(), 'DB', 'Collection');
    }

    public function testHandle()
    {
        $mongo = $this->getMock('Mongo', array('selectCollection'), array(), '', false);
        $collection = $this->getMock('stdClass', array('save'));

        $mongo->expects($this->once())
            ->method('selectCollection')
            ->with('DB', 'Collection')
            ->will($this->returnValue($collection));

        $record = $this->getRecord(Logger::WARNING, 'test', array('data' => new \stdClass, 'foo' => 34));

        $expected = array(
            'message' => 'test',
            'context' => array('data' => '[object] (stdClass: {})', 'foo' => 34),
            'level' => Logger::WARNING,
            'level_name' => 'WARNING',
            'channel' => 'test',
            'datetime' => $record['datetime']->format('Y-m-d H:i:s'),
            'extra' => array(),
        );

        $collection->expects($this->once())
            ->method('save')
            ->with($expected);

        $handler = new MongoDBHandler($mongo, 'DB', 'Collection');
        $handler->handle($record);
    }
}

if (!class_exists('Mongo')) {
    class Mongo
    {
        public function selectCollection()
        {
        }
    }
}
