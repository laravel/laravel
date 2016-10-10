<?php
namespace GuzzleHttp\Tests\Http;

use GuzzleHttp\Stream\FnStream;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Stream\LimitStream;
use GuzzleHttp\Stream\NoSeekStream;

/**
 * @covers GuzzleHttp\Stream\LimitStream
 */
class LimitStreamTest extends \PHPUnit_Framework_TestCase
{
    /** @var LimitStream */
    protected $body;

    /** @var Stream */
    protected $decorated;

    public function setUp()
    {
        $this->decorated = Stream::factory(fopen(__FILE__, 'r'));
        $this->body = new LimitStream($this->decorated, 10, 3);
    }

    public function testReturnsSubset()
    {
        $body = new LimitStream(Stream::factory('foo'), -1, 1);
        $this->assertEquals('oo', (string) $body);
        $this->assertTrue($body->eof());
        $body->seek(0);
        $this->assertFalse($body->eof());
        $this->assertEquals('oo', $body->read(100));
        $this->assertTrue($body->eof());
    }

    public function testReturnsSubsetWhenCastToString()
    {
        $body = Stream::factory('foo_baz_bar');
        $limited = new LimitStream($body, 3, 4);
        $this->assertEquals('baz', (string) $limited);
    }

    public function testReturnsSubsetOfEmptyBodyWhenCastToString()
    {
        $body = Stream::factory('');
        $limited = new LimitStream($body, 0, 10);
        $this->assertEquals('', (string) $limited);
    }

    public function testSeeksWhenConstructed()
    {
        $this->assertEquals(0, $this->body->tell());
        $this->assertEquals(3, $this->decorated->tell());
    }

    public function testAllowsBoundedSeek()
    {
        $this->assertEquals(true, $this->body->seek(100));
        $this->assertEquals(10, $this->body->tell());
        $this->assertEquals(13, $this->decorated->tell());
        $this->assertEquals(true, $this->body->seek(0));
        $this->assertEquals(0, $this->body->tell());
        $this->assertEquals(3, $this->decorated->tell());
        $this->assertEquals(false, $this->body->seek(-10));
        $this->assertEquals(0, $this->body->tell());
        $this->assertEquals(3, $this->decorated->tell());
        $this->assertEquals(true, $this->body->seek(5));
        $this->assertEquals(5, $this->body->tell());
        $this->assertEquals(8, $this->decorated->tell());
        $this->assertEquals(false, $this->body->seek(1000, SEEK_END));
    }

    public function testReadsOnlySubsetOfData()
    {
        $data = $this->body->read(100);
        $this->assertEquals(10, strlen($data));
        $this->assertFalse($this->body->read(1000));

        $this->body->setOffset(10);
        $newData = $this->body->read(100);
        $this->assertEquals(10, strlen($newData));
        $this->assertNotSame($data, $newData);
    }

    /**
     * @expectedException \GuzzleHttp\Stream\Exception\SeekException
     * @expectedExceptionMessage Could not seek the stream to position 2
     */
    public function testThrowsWhenCurrentGreaterThanOffsetSeek()
    {
        $a = Stream::factory('foo_bar');
        $b = new NoSeekStream($a);
        $c = new LimitStream($b);
        $a->getContents();
        $c->setOffset(2);
    }

    public function testClaimsConsumedWhenReadLimitIsReached()
    {
        $this->assertFalse($this->body->eof());
        $this->body->read(1000);
        $this->assertTrue($this->body->eof());
    }

    public function testContentLengthIsBounded()
    {
        $this->assertEquals(10, $this->body->getSize());
    }

    public function testGetContentsIsBasedOnSubset()
    {
        $body = new LimitStream(Stream::factory('foobazbar'), 3, 3);
        $this->assertEquals('baz', $body->getContents());
    }

    public function testReturnsNullIfSizeCannotBeDetermined()
    {
        $a = new FnStream([
            'getSize' => function () { return null; },
            'tell'    => function () { return 0; },
        ]);
        $b = new LimitStream($a);
        $this->assertNull($b->getSize());
    }

    public function testLengthLessOffsetWhenNoLimitSize()
    {
        $a = Stream::factory('foo_bar');
        $b = new LimitStream($a, -1, 4);
        $this->assertEquals(3, $b->getSize());
    }
}
