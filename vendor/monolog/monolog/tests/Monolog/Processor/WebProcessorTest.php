<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\TestCase;

class WebProcessorTest extends TestCase
{
    public function testProcessor()
    {
        $server = array(
            'REQUEST_URI'    => 'A',
            'REMOTE_ADDR'    => 'B',
            'REQUEST_METHOD' => 'C',
            'HTTP_REFERER'   => 'D',
            'SERVER_NAME'    => 'F',
            'UNIQUE_ID'      => 'G',
        );

        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertEquals($server['REQUEST_URI'], $record['extra']['url']);
        $this->assertEquals($server['REMOTE_ADDR'], $record['extra']['ip']);
        $this->assertEquals($server['REQUEST_METHOD'], $record['extra']['http_method']);
        $this->assertEquals($server['HTTP_REFERER'], $record['extra']['referrer']);
        $this->assertEquals($server['SERVER_NAME'], $record['extra']['server']);
        $this->assertEquals($server['UNIQUE_ID'], $record['extra']['unique_id']);
    }

    public function testProcessorDoNothingIfNoRequestUri()
    {
        $server = array(
            'REMOTE_ADDR'    => 'B',
            'REQUEST_METHOD' => 'C',
        );
        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertEmpty($record['extra']);
    }

    public function testProcessorReturnNullIfNoHttpReferer()
    {
        $server = array(
            'REQUEST_URI'    => 'A',
            'REMOTE_ADDR'    => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME'    => 'F',
        );
        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertNull($record['extra']['referrer']);
    }

    public function testProcessorDoesNotAddUniqueIdIfNotPresent()
    {
        $server = array(
            'REQUEST_URI'    => 'A',
            'REMOTE_ADDR'    => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME'    => 'F',
        );
        $processor = new WebProcessor($server);
        $record = $processor($this->getRecord());
        $this->assertFalse(isset($record['extra']['unique_id']));
    }

    public function testProcessorAddsOnlyRequestedExtraFields()
    {
        $server = array(
            'REQUEST_URI'    => 'A',
            'REMOTE_ADDR'    => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME'    => 'F',
        );

        $processor = new WebProcessor($server, array('url', 'http_method'));
        $record = $processor($this->getRecord());

        $this->assertSame(array('url' => 'A', 'http_method' => 'C'), $record['extra']);
    }

    public function testProcessorConfiguringOfExtraFields()
    {
        $server = array(
            'REQUEST_URI'    => 'A',
            'REMOTE_ADDR'    => 'B',
            'REQUEST_METHOD' => 'C',
            'SERVER_NAME'    => 'F',
        );

        $processor = new WebProcessor($server, array('url' => 'REMOTE_ADDR'));
        $record = $processor($this->getRecord());

        $this->assertSame(array('url' => 'B'), $record['extra']);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testInvalidData()
    {
        new WebProcessor(new \stdClass);
    }
}
