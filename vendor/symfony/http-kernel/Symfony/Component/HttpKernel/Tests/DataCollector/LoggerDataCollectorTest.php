<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;

class LoggerDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCollectTestData
     */
    public function testCollect($nb, $logs, $expectedLogs, $expectedDeprecationCount)
    {
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\DebugLoggerInterface');
        $logger->expects($this->once())->method('countErrors')->will($this->returnValue($nb));
        $logger->expects($this->exactly(2))->method('getLogs')->will($this->returnValue($logs));

        $c = new LoggerDataCollector($logger);
        $c->lateCollect();

        $this->assertSame('logger', $c->getName());
        $this->assertSame($nb, $c->countErrors());
        $this->assertSame($expectedLogs ? $expectedLogs : $logs, $c->getLogs());
        $this->assertSame($expectedDeprecationCount, $c->countDeprecations());
    }

    public function getCollectTestData()
    {
        return array(
            array(
                1,
                array(array('message' => 'foo', 'context' => array())),
                null,
                0,
            ),
            array(
                1,
                array(array('message' => 'foo', 'context' => array('foo' => fopen(__FILE__, 'r')))),
                array(array('message' => 'foo', 'context' => array('foo' => 'Resource(stream)'))),
                0,
            ),
            array(
                1,
                array(array('message' => 'foo', 'context' => array('foo' => new \stdClass()))),
                array(array('message' => 'foo', 'context' => array('foo' => 'Object(stdClass)'))),
                0,
            ),
            array(
                1,
                array(
                    array('message' => 'foo', 'context' => array('type' => ErrorHandler::TYPE_DEPRECATION)),
                    array('message' => 'foo2', 'context' => array('type' => ErrorHandler::TYPE_DEPRECATION)),
                ),
                null,
                2,
            ),
        );
    }
}
