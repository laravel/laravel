<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\AsyncReadStream;
use GuzzleHttp\Stream\BufferStream;
use GuzzleHttp\Stream\FnStream;
use GuzzleHttp\Stream\Stream;

class AsyncReadStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Buffer must be readable and writable
     */
    public function testValidatesReadableBuffer()
    {
        new AsyncReadStream(FnStream::decorate(
            Stream::factory(),
            ['isReadable' => function () { return false; }]
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Buffer must be readable and writable
     */
    public function testValidatesWritableBuffer()
    {
        new AsyncReadStream(FnStream::decorate(
            Stream::factory(),
            ['isWritable' => function () { return false; }]
        ));
    }

    public function testValidatesHwmMetadata()
    {
        $a = new AsyncReadStream(Stream::factory(), [
            'drain' => function() {}
        ]);
        $this->assertNull($this->readAttribute($a, 'drain'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage pump must be callable
     */
    public function testValidatesPumpIsCallable()
    {
        new AsyncReadStream(new BufferStream(), ['pump' => true]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage drain must be callable
     */
    public function testValidatesDrainIsCallable()
    {
        new AsyncReadStream(new BufferStream(), ['drain' => true]);
    }

    public function testCanInitialize()
    {
        $buffer = new BufferStream();
        $a = new AsyncReadStream($buffer, [
            'size'  => 10,
            'drain' => function () {},
            'pump'  => function () {},
        ]);
        $this->assertSame($buffer, $this->readAttribute($a, 'stream'));
        $this->assertTrue(is_callable($this->readAttribute($a, 'drain')));
        $this->assertTrue(is_callable($this->readAttribute($a, 'pump')));
        $this->assertTrue($a->isReadable());
        $this->assertFalse($a->isSeekable());
        $this->assertFalse($a->isWritable());
        $this->assertFalse($a->write('foo'));
        $this->assertEquals(10, $a->getSize());
    }

    public function testReadsFromBufferWithNoDrainOrPump()
    {
        $buffer = new BufferStream();
        $a = new AsyncReadStream($buffer);
        $buffer->write('foo');
        $this->assertNull($a->getSize());
        $this->assertEquals('foo', $a->read(10));
        $this->assertEquals('', $a->read(10));
    }

    public function testCallsPumpForMoreDataWhenRequested()
    {
        $called = 0;
        $buffer = new BufferStream();
        $a = new AsyncReadStream($buffer, [
            'pump' => function ($size) use (&$called) {
                $called++;
                return str_repeat('.', $size);
            }
        ]);
        $buffer->write('foobar');
        $this->assertEquals('foo', $a->read(3));
        $this->assertEquals(0, $called);
        $this->assertEquals('bar.....', $a->read(8));
        $this->assertEquals(1, $called);
        $this->assertEquals('..', $a->read(2));
        $this->assertEquals(2, $called);
    }

    public function testCallsDrainWhenNeeded()
    {
        $called = 0;
        $buffer = new BufferStream(5);
        $a = new AsyncReadStream($buffer, [
            'drain' => function (BufferStream $b) use (&$called, $buffer) {
                $this->assertSame($b, $buffer);
                $called++;
            }
        ]);

        $buffer->write('foobar');
        $this->assertEquals(6, $buffer->getSize());
        $this->assertEquals(0, $called);

        $a->read(3);
        $this->assertTrue($this->readAttribute($a, 'needsDrain'));
        $this->assertEquals(3, $buffer->getSize());
        $this->assertEquals(0, $called);

        $a->read(3);
        $this->assertEquals(0, $buffer->getSize());
        $this->assertFalse($this->readAttribute($a, 'needsDrain'));
        $this->assertEquals(1, $called);
    }

    public function testCreatesBufferWithNoConfig()
    {
        list($buffer, $async) = AsyncReadStream::create();
        $this->assertInstanceOf('GuzzleHttp\Stream\BufferStream', $buffer);
        $this->assertInstanceOf('GuzzleHttp\Stream\AsyncReadStream', $async);
    }

    public function testCreatesBufferWithSpecifiedBuffer()
    {
        $buf = new BufferStream();
        list($buffer, $async) = AsyncReadStream::create(['buffer' => $buf]);
        $this->assertSame($buf, $buffer);
        $this->assertInstanceOf('GuzzleHttp\Stream\AsyncReadStream', $async);
    }

    public function testCreatesNullStream()
    {
        list($buffer, $async) = AsyncReadStream::create(['max_buffer' => 0]);
        $this->assertInstanceOf('GuzzleHttp\Stream\NullStream', $buffer);
        $this->assertInstanceOf('GuzzleHttp\Stream\AsyncReadStream', $async);
    }

    public function testCreatesDroppingStream()
    {
        list($buffer, $async) = AsyncReadStream::create(['max_buffer' => 5]);
        $this->assertInstanceOf('GuzzleHttp\Stream\DroppingStream', $buffer);
        $this->assertInstanceOf('GuzzleHttp\Stream\AsyncReadStream', $async);
        $buffer->write('12345678910');
        $this->assertEquals(5, $buffer->getSize());
    }

    public function testCreatesOnWriteStream()
    {
        $c = 0;
        $b = new BufferStream();
        list($buffer, $async) = AsyncReadStream::create([
            'buffer' => $b,
            'write'  => function (BufferStream $buf, $data) use (&$c, $b) {
                $this->assertSame($buf, $b);
                $this->assertEquals('foo', $data);
                $c++;
            }
        ]);
        $this->assertInstanceOf('GuzzleHttp\Stream\FnStream', $buffer);
        $this->assertInstanceOf('GuzzleHttp\Stream\AsyncReadStream', $async);
        $this->assertEquals(0, $c);
        $this->assertEquals(3, $buffer->write('foo'));
        $this->assertEquals(1, $c);
        $this->assertEquals(3, $buffer->write('foo'));
        $this->assertEquals(2, $c);
        $this->assertEquals('foofoo', (string) $buffer);
    }
}
