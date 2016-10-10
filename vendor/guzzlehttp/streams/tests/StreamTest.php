<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\Stream;

/**
 * @covers GuzzleHttp\Stream\Stream
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsExceptionOnInvalidArgument()
    {
        new Stream(true);
    }

    public function testConstructorInitializesProperties()
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, 'data');
        $stream = new Stream($handle);
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isSeekable());
        $this->assertEquals('php://temp', $stream->getMetadata('uri'));
        $this->assertInternalType('array', $stream->getMetadata());
        $this->assertEquals(4, $stream->getSize());
        $this->assertFalse($stream->eof());
        $stream->close();
    }

    public function testStreamClosesHandleOnDestruct()
    {
        $handle = fopen('php://temp', 'r');
        $stream = new Stream($handle);
        unset($stream);
        $this->assertFalse(is_resource($handle));
    }

    public function testConvertsToString()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'data');
        $stream = new Stream($handle);
        $this->assertEquals('data', (string) $stream);
        $this->assertEquals('data', (string) $stream);
        $stream->close();
    }

    public function testGetsContents()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'data');
        $stream = new Stream($handle);
        $this->assertEquals('', $stream->getContents());
        $stream->seek(0);
        $this->assertEquals('data', $stream->getContents());
        $this->assertEquals('', $stream->getContents());
    }

    public function testChecksEof()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'data');
        $stream = new Stream($handle);
        $this->assertFalse($stream->eof());
        $stream->read(4);
        $this->assertTrue($stream->eof());
        $stream->close();
    }

    public function testAllowsSettingManualSize()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'data');
        $stream = new Stream($handle);
        $stream->setSize(10);
        $this->assertEquals(10, $stream->getSize());
        $stream->close();
    }

    public function testGetSize()
    {
        $size = filesize(__FILE__);
        $handle = fopen(__FILE__, 'r');
        $stream = new Stream($handle);
        $this->assertEquals($size, $stream->getSize());
        // Load from cache
        $this->assertEquals($size, $stream->getSize());
        $stream->close();
    }

    public function testEnsuresSizeIsConsistent()
    {
        $h = fopen('php://temp', 'w+');
        $this->assertEquals(3, fwrite($h, 'foo'));
        $stream = new Stream($h);
        $this->assertEquals(3, $stream->getSize());
        $this->assertEquals(4, $stream->write('test'));
        $this->assertEquals(7, $stream->getSize());
        $this->assertEquals(7, $stream->getSize());
        $stream->close();
    }

    public function testProvidesStreamPosition()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);
        $this->assertEquals(0, $stream->tell());
        $stream->write('foo');
        $this->assertEquals(3, $stream->tell());
        $stream->seek(1);
        $this->assertEquals(1, $stream->tell());
        $this->assertSame(ftell($handle), $stream->tell());
        $stream->close();
    }

    public function testKeepsPositionOfResource()
    {
        $h = fopen(__FILE__, 'r');
        fseek($h, 10);
        $stream = Stream::factory($h);
        $this->assertEquals(10, $stream->tell());
        $stream->close();
    }

    public function testCanDetachAndAttachStream()
    {
        $r = fopen('php://temp', 'w+');
        $stream = new Stream($r);
        $stream->write('foo');
        $this->assertTrue($stream->isReadable());
        $this->assertSame($r, $stream->detach());
        $this->assertNull($stream->detach());

        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->read(10));
        $this->assertFalse($stream->isWritable());
        $this->assertFalse($stream->write('bar'));
        $this->assertFalse($stream->isSeekable());
        $this->assertFalse($stream->seek(10));
        $this->assertFalse($stream->tell());
        $this->assertTrue($stream->eof());
        $this->assertNull($stream->getSize());
        $this->assertSame('', (string) $stream);
        $this->assertSame('', $stream->getContents());

        $stream->attach($r);
        $stream->seek(0);
        $this->assertEquals('foo', $stream->getContents());
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isSeekable());

        $stream->close();
    }

    public function testCloseClearProperties()
    {
        $handle = fopen('php://temp', 'r+');
        $stream = new Stream($handle);
        $stream->close();

        $this->assertEmpty($stream->getMetadata());
        $this->assertFalse($stream->isSeekable());
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $this->assertNull($stream->getSize());
    }

    public function testCreatesWithFactory()
    {
        $stream = Stream::factory('foo');
        $this->assertInstanceOf('GuzzleHttp\Stream\Stream', $stream);
        $this->assertEquals('foo', $stream->getContents());
        $stream->close();
    }

    public function testFactoryCreatesFromEmptyString()
    {
        $s = Stream::factory();
        $this->assertInstanceOf('GuzzleHttp\Stream\Stream', $s);
    }

    public function testFactoryCreatesFromResource()
    {
        $r = fopen(__FILE__, 'r');
        $s = Stream::factory($r);
        $this->assertInstanceOf('GuzzleHttp\Stream\Stream', $s);
        $this->assertSame(file_get_contents(__FILE__), (string) $s);
    }

    public function testFactoryCreatesFromObjectWithToString()
    {
        $r = new HasToString();
        $s = Stream::factory($r);
        $this->assertInstanceOf('GuzzleHttp\Stream\Stream', $s);
        $this->assertEquals('foo', (string) $s);
    }

    public function testCreatePassesThrough()
    {
        $s = Stream::factory('foo');
        $this->assertSame($s, Stream::factory($s));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionForUnknown()
    {
        Stream::factory(new \stdClass());
    }

    public function testReturnsCustomMetadata()
    {
        $s = Stream::factory('foo', ['metadata' => ['hwm' => 3]]);
        $this->assertEquals(3, $s->getMetadata('hwm'));
        $this->assertArrayHasKey('hwm', $s->getMetadata());
    }

    public function testCanSetSize()
    {
        $s = Stream::factory('', ['size' => 10]);
        $this->assertEquals(10, $s->getSize());
    }

    public function testCanCreateIteratorBasedStream()
    {
        $a = new \ArrayIterator(['foo', 'bar', '123']);
        $p = Stream::factory($a);
        $this->assertInstanceOf('GuzzleHttp\Stream\PumpStream', $p);
        $this->assertEquals('foo', $p->read(3));
        $this->assertFalse($p->eof());
        $this->assertEquals('b', $p->read(1));
        $this->assertEquals('a', $p->read(1));
        $this->assertEquals('r12', $p->read(3));
        $this->assertFalse($p->eof());
        $this->assertEquals('3', $p->getContents());
        $this->assertTrue($p->eof());
        $this->assertEquals(9, $p->tell());
    }
}

class HasToString
{
    public function __toString() {
        return 'foo';
    }
}
