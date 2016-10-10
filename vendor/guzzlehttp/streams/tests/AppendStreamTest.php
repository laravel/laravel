<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\AppendStream;
use GuzzleHttp\Stream\Stream;

class AppendStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Each stream must be readable
     */
    public function testValidatesStreamsAreReadable()
    {
        $a = new AppendStream();
        $s = $this->getMockBuilder('GuzzleHttp\Stream\StreamInterface')
            ->setMethods(['isReadable'])
            ->getMockForAbstractClass();
        $s->expects($this->once())
            ->method('isReadable')
            ->will($this->returnValue(false));
        $a->addStream($s);
    }

    public function testValidatesSeekType()
    {
        $a = new AppendStream();
        $this->assertFalse($a->seek(100, SEEK_CUR));
    }

    public function testTriesToRewindOnSeek()
    {
        $a = new AppendStream();
        $s = $this->getMockBuilder('GuzzleHttp\Stream\StreamInterface')
            ->setMethods(['isReadable', 'seek', 'isSeekable'])
            ->getMockForAbstractClass();
        $s->expects($this->once())
            ->method('isReadable')
            ->will($this->returnValue(true));
        $s->expects($this->once())
            ->method('isSeekable')
            ->will($this->returnValue(true));
        $s->expects($this->once())
            ->method('seek')
            ->will($this->returnValue(false));
        $a->addStream($s);
        $this->assertFalse($a->seek(10));
    }

    public function testSeeksToPositionByReading()
    {
        $a = new AppendStream([
            Stream::factory('foo'),
            Stream::factory('bar'),
            Stream::factory('baz'),
        ]);

        $this->assertTrue($a->seek(3));
        $this->assertEquals(3, $a->tell());
        $this->assertEquals('bar', $a->read(3));
        $a->seek(6);
        $this->assertEquals(6, $a->tell());
        $this->assertEquals('baz', $a->read(3));
    }

    public function testDetachesEachStream()
    {
        $s1 = Stream::factory('foo');
        $s2 = Stream::factory('foo');
        $a = new AppendStream([$s1, $s2]);
        $this->assertSame('foofoo', (string) $a);
        $a->detach();
        $this->assertSame('', (string) $a);
        $this->assertSame(0, $a->getSize());
    }

    public function testClosesEachStream()
    {
        $s1 = Stream::factory('foo');
        $a = new AppendStream([$s1]);
        $a->close();
        $this->assertSame('', (string) $a);
    }

    public function testIsNotWritable()
    {
        $a = new AppendStream([Stream::factory('foo')]);
        $this->assertFalse($a->isWritable());
        $this->assertTrue($a->isSeekable());
        $this->assertTrue($a->isReadable());
        $this->assertFalse($a->write('foo'));
    }

    public function testDoesNotNeedStreams()
    {
        $a = new AppendStream();
        $this->assertEquals('', (string) $a);
    }

    public function testCanReadFromMultipleStreams()
    {
        $a = new AppendStream([
            Stream::factory('foo'),
            Stream::factory('bar'),
            Stream::factory('baz'),
        ]);
        $this->assertFalse($a->eof());
        $this->assertSame(0, $a->tell());
        $this->assertEquals('foo', $a->read(3));
        $this->assertEquals('bar', $a->read(3));
        $this->assertEquals('baz', $a->read(3));
        $this->assertTrue($a->eof());
        $this->assertSame(9, $a->tell());
        $this->assertEquals('foobarbaz', (string) $a);
    }

    public function testCanDetermineSizeFromMultipleStreams()
    {
        $a = new AppendStream([
            Stream::factory('foo'),
            Stream::factory('bar')
        ]);
        $this->assertEquals(6, $a->getSize());

        $s = $this->getMockBuilder('GuzzleHttp\Stream\StreamInterface')
            ->setMethods(['isSeekable', 'isReadable'])
            ->getMockForAbstractClass();
        $s->expects($this->once())
            ->method('isSeekable')
            ->will($this->returnValue(null));
        $s->expects($this->once())
            ->method('isReadable')
            ->will($this->returnValue(true));
        $a->addStream($s);
        $this->assertNull($a->getSize());
    }

    public function testCatchesExceptionsWhenCastingToString()
    {
        $s = $this->getMockBuilder('GuzzleHttp\Stream\StreamInterface')
            ->setMethods(['read', 'isReadable', 'eof'])
            ->getMockForAbstractClass();
        $s->expects($this->once())
            ->method('read')
            ->will($this->throwException(new \RuntimeException('foo')));
        $s->expects($this->once())
            ->method('isReadable')
            ->will($this->returnValue(true));
        $s->expects($this->any())
            ->method('eof')
            ->will($this->returnValue(false));
        $a = new AppendStream([$s]);
        $this->assertFalse($a->eof());
        $this->assertSame('', (string) $a);
    }

    public function testCanDetach()
    {
        $s = new AppendStream();
        $s->detach();
    }

    public function testReturnsEmptyMetadata()
    {
        $s = new AppendStream();
        $this->assertEquals([], $s->getMetadata());
        $this->assertNull($s->getMetadata('foo'));
    }

    /**
     * @expectedException \GuzzleHttp\Stream\Exception\CannotAttachException
     */
    public function testCannotAttach()
    {
        $p = new AppendStream();
        $p->attach('a');
    }
}
