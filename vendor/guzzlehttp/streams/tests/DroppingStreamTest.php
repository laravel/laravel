<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\BufferStream;
use GuzzleHttp\Stream\DroppingStream;

class DroppingStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testBeginsDroppingWhenSizeExceeded()
    {
        $stream = new BufferStream();
        $drop = new DroppingStream($stream, 5);
        $this->assertEquals(3, $drop->write('hel'));
        $this->assertFalse($drop->write('lo'));
        $this->assertEquals(5, $drop->getSize());
        $this->assertEquals('hello', $drop->read(5));
        $this->assertEquals(0, $drop->getSize());
        $drop->write('12345678910');
        $this->assertEquals(5, $stream->getSize());
        $this->assertEquals(5, $drop->getSize());
        $this->assertEquals('12345', (string) $drop);
        $this->assertEquals(0, $drop->getSize());
        $drop->write('hello');
        $this->assertFalse($drop->write('test'));
    }
}
