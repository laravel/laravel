<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Stream\StreamDecoratorTrait;

class Str implements StreamInterface
{
    use StreamDecoratorTrait;
}

/**
 * @covers GuzzleHttp\Stream\StreamDecoratorTrait
 */
class StreamDecoratorTraitTest extends \PHPUnit_Framework_TestCase
{
    private $a;
    private $b;
    private $c;

    public function setUp()
    {
        $this->c = fopen('php://temp', 'r+');
        fwrite($this->c, 'foo');
        fseek($this->c, 0);
        $this->a = Stream::factory($this->c);
        $this->b = new Str($this->a);
    }

    public function testCatchesExceptionsWhenCastingToString()
    {
        $s = $this->getMockBuilder('GuzzleHttp\Stream\StreamInterface')
            ->setMethods(['read'])
            ->getMockForAbstractClass();
        $s->expects($this->once())
            ->method('read')
            ->will($this->throwException(new \Exception('foo')));
        $msg = '';
        set_error_handler(function ($errNo, $str) use (&$msg) { $msg = $str; });
        echo new Str($s);
        restore_error_handler();
        $this->assertContains('foo', $msg);
    }

    public function testToString()
    {
        $this->assertEquals('foo', (string) $this->b);
    }

    public function testHasSize()
    {
        $this->assertEquals(3, $this->b->getSize());
        $this->assertSame($this->b, $this->b->setSize(2));
        $this->assertEquals(2, $this->b->getSize());
    }

    public function testReads()
    {
        $this->assertEquals('foo', $this->b->read(10));
    }

    public function testCheckMethods()
    {
        $this->assertEquals($this->a->isReadable(), $this->b->isReadable());
        $this->assertEquals($this->a->isWritable(), $this->b->isWritable());
        $this->assertEquals($this->a->isSeekable(), $this->b->isSeekable());
    }

    public function testSeeksAndTells()
    {
        $this->assertTrue($this->b->seek(1));
        $this->assertEquals(1, $this->a->tell());
        $this->assertEquals(1, $this->b->tell());
        $this->assertTrue($this->b->seek(0));
        $this->assertEquals(0, $this->a->tell());
        $this->assertEquals(0, $this->b->tell());
        $this->assertTrue($this->b->seek(0, SEEK_END));
        $this->assertEquals(3, $this->a->tell());
        $this->assertEquals(3, $this->b->tell());
    }

    public function testGetsContents()
    {
        $this->assertEquals('foo', $this->b->getContents());
        $this->assertEquals('', $this->b->getContents());
        $this->b->seek(1);
        $this->assertEquals('oo', $this->b->getContents(1));
    }

    public function testCloses()
    {
        $this->b->close();
        $this->assertFalse(is_resource($this->c));
    }

    public function testDetaches()
    {
        $this->b->detach();
        $this->assertFalse($this->b->isReadable());
    }

    /**
     * @expectedException \GuzzleHttp\Stream\Exception\CannotAttachException
     */
    public function testCannotAttachByDefault()
    {
        $this->b->attach('a');
    }

    public function testWrapsMetadata()
    {
        $this->assertSame($this->b->getMetadata(), $this->a->getMetadata());
        $this->assertSame($this->b->getMetadata('uri'), $this->a->getMetadata('uri'));
    }

    public function testWrapsWrites()
    {
        $this->b->seek(0, SEEK_END);
        $this->b->write('foo');
        $this->assertEquals('foofoo', (string) $this->a);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testThrowsWithInvalidGetter()
    {
        $this->b->foo;
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testThrowsWhenGetterNotImplemented()
    {
        $s = new BadStream();
        $s->stream;
    }
}

class BadStream
{
    use StreamDecoratorTrait;

    public function __construct() {}
}
