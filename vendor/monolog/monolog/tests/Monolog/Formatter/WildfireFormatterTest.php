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

class WildfireFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function testDefaultFormat()
    {
        $wildfire = new WildfireFormatter();
        $record = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new \DateTime("@0"),
            'extra' => array('ip' => '127.0.0.1'),
            'message' => 'log',
        );

        $message = $wildfire->format($record);

        $this->assertEquals(
            '125|[{"Type":"ERROR","File":"","Line":"","Label":"meh"},'
                .'{"message":"log","context":{"from":"logger"},"extra":{"ip":"127.0.0.1"}}]|',
            $message
        );
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function testFormatWithFileAndLine()
    {
        $wildfire = new WildfireFormatter();
        $record = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new \DateTime("@0"),
            'extra' => array('ip' => '127.0.0.1', 'file' => 'test', 'line' => 14),
            'message' => 'log',
        );

        $message = $wildfire->format($record);

        $this->assertEquals(
            '129|[{"Type":"ERROR","File":"test","Line":14,"Label":"meh"},'
                .'{"message":"log","context":{"from":"logger"},"extra":{"ip":"127.0.0.1"}}]|',
            $message
        );
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function testFormatWithoutContext()
    {
        $wildfire = new WildfireFormatter();
        $record = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new \DateTime("@0"),
            'extra' => array(),
            'message' => 'log',
        );

        $message = $wildfire->format($record);

        $this->assertEquals(
            '58|[{"Type":"ERROR","File":"","Line":"","Label":"meh"},"log"]|',
            $message
        );
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::formatBatch
     * @expectedException BadMethodCallException
     */
    public function testBatchFormatThrowException()
    {
        $wildfire = new WildfireFormatter();
        $record = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new \DateTime("@0"),
            'extra' => array(),
            'message' => 'log',
        );

        $wildfire->formatBatch(array($record));
    }

    /**
     * @covers Monolog\Formatter\WildfireFormatter::format
     */
    public function testTableFormat()
    {
        $wildfire = new WildfireFormatter();
        $record = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'table-channel',
            'context' => array(
            WildfireFormatter::TABLE => array(
                    array('col1', 'col2', 'col3'),
                    array('val1', 'val2', 'val3'),
                    array('foo1', 'foo2', 'foo3'),
                    array('bar1', 'bar2', 'bar3'),
                ),
            ),
            'datetime' => new \DateTime("@0"),
            'extra' => array(),
            'message' => 'table-message',
        );

        $message = $wildfire->format($record);

        $this->assertEquals(
            '171|[{"Type":"TABLE","File":"","Line":"","Label":"table-channel: table-message"},[["col1","col2","col3"],["val1","val2","val3"],["foo1","foo2","foo3"],["bar1","bar2","bar3"]]]|',
            $message
        );
    }
}
