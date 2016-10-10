<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\LazyOpenStream;

class LazyOpenStreamTest extends \PHPUnit_Framework_TestCase
{
    private $fname;

    public function setup()
    {
        $this->fname = tempnam('/tmp', 'tfile');

        if (file_exists($this->fname)) {
            unlink($this->fname);
        }
    }

    public function tearDown()
    {
        if (file_exists($this->fname)) {
            unlink($this->fname);
        }
    }

    public function testOpensLazily()
    {
        $l = new LazyOpenStream($this->fname, 'w+');
        $l->write('foo');
        $this->assertInternalType('array', $l->getMetadata());
        $this->assertFileExists($this->fname);
        $this->assertEquals('foo', file_get_contents($this->fname));
        $this->assertEquals('foo', (string) $l);
    }

    public function testProxiesToFile()
    {
        file_put_contents($this->fname, 'foo');
        $l = new LazyOpenStream($this->fname, 'r');
        $this->assertEquals('foo', $l->read(4));
        $this->assertTrue($l->eof());
        $this->assertEquals(3, $l->tell());
        $this->assertTrue($l->isReadable());
        $this->assertTrue($l->isSeekable());
        $this->assertFalse($l->isWritable());
        $l->seek(1);
        $this->assertEquals('oo', $l->getContents());
        $this->assertEquals('foo', (string) $l);
        $this->assertEquals(3, $l->getSize());
        $this->assertInternalType('array', $l->getMetadata());
        $l->close();
    }

    public function testDetachesUnderlyingStream()
    {
        file_put_contents($this->fname, 'foo');
        $l = new LazyOpenStream($this->fname, 'r');
        $r = $l->detach();
        $this->assertInternalType('resource', $r);
        fseek($r, 0);
        $this->assertEquals('foo', stream_get_contents($r));
        fclose($r);
    }
}
