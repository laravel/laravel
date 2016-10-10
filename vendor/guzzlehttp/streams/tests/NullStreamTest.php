<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\NullStream;

class NullStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesNothing()
    {
        $b = new NullStream();
        $this->assertEquals('', $b->read(10));
        $this->assertEquals(4, $b->write('test'));
        $this->assertEquals('', (string) $b);
        $this->assertNull($b->getMetadata('a'));
        $this->assertEquals([], $b->getMetadata());
        $this->assertEquals(0, $b->getSize());
        $this->assertEquals('', $b->getContents());
        $this->assertEquals(0, $b->tell());

        $this->assertTrue($b->isReadable());
        $this->assertTrue($b->isWritable());
        $this->assertTrue($b->isSeekable());
        $this->assertFalse($b->seek(10));

        $this->assertTrue($b->eof());
        $b->detach();
        $this->assertTrue($b->eof());
        $b->close();
    }

    /**
     * @expectedException \GuzzleHttp\Stream\Exception\CannotAttachException
     */
    public function testCannotAttach()
    {
        $p = new NullStream();
        $p->attach('a');
    }
}
