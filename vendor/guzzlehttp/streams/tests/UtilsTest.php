<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\FnStream;
use GuzzleHttp\Stream\NoSeekStream;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Stream\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testCopiesToString()
    {
        $s = Stream::factory('foobaz');
        $this->assertEquals('foobaz', Utils::copyToString($s));
        $s->seek(0);
        $this->assertEquals('foo', Utils::copyToString($s, 3));
        $this->assertEquals('baz', Utils::copyToString($s, 3));
        $this->assertEquals('', Utils::copyToString($s));
    }

    public function testCopiesToStringStopsWhenReadFails()
    {
        $s1 = Stream::factory('foobaz');
        $s1 = FnStream::decorate($s1, [
            'read' => function () {
                return false;
            }
        ]);
        $result = Utils::copyToString($s1);
        $this->assertEquals('', $result);
    }

    public function testCopiesToStream()
    {
        $s1 = Stream::factory('foobaz');
        $s2 = Stream::factory('');
        Utils::copyToStream($s1, $s2);
        $this->assertEquals('foobaz', (string) $s2);
        $s2 = Stream::factory('');
        $s1->seek(0);
        Utils::copyToStream($s1, $s2, 3);
        $this->assertEquals('foo', (string) $s2);
        Utils::copyToStream($s1, $s2, 3);
        $this->assertEquals('foobaz', (string) $s2);
    }

    public function testStopsCopyToStreamWhenWriteFails()
    {
        $s1 = Stream::factory('foobaz');
        $s2 = Stream::factory('');
        $s2 = FnStream::decorate($s2, ['write' => function () { return 0; }]);
        Utils::copyToStream($s1, $s2);
        $this->assertEquals('', (string) $s2);
    }

    public function testStopsCopyToSteamWhenWriteFailsWithMaxLen()
    {
        $s1 = Stream::factory('foobaz');
        $s2 = Stream::factory('');
        $s2 = FnStream::decorate($s2, ['write' => function () { return 0; }]);
        Utils::copyToStream($s1, $s2, 10);
        $this->assertEquals('', (string) $s2);
    }

    public function testStopsCopyToSteamWhenReadFailsWithMaxLen()
    {
        $s1 = Stream::factory('foobaz');
        $s1 = FnStream::decorate($s1, ['read' => function () { return ''; }]);
        $s2 = Stream::factory('');
        Utils::copyToStream($s1, $s2, 10);
        $this->assertEquals('', (string) $s2);
    }

    public function testReadsLines()
    {
        $s = Stream::factory("foo\nbaz\nbar");
        $this->assertEquals("foo\n", Utils::readline($s));
        $this->assertEquals("baz\n", Utils::readline($s));
        $this->assertEquals("bar", Utils::readline($s));
    }

    public function testReadsLinesUpToMaxLength()
    {
        $s = Stream::factory("12345\n");
        $this->assertEquals("123", Utils::readline($s, 4));
        $this->assertEquals("45\n", Utils::readline($s));
    }

    public function testReadsLineUntilFalseReturnedFromRead()
    {
        $s = $this->getMockBuilder('GuzzleHttp\Stream\Stream')
            ->setMethods(['read', 'eof'])
            ->disableOriginalConstructor()
            ->getMock();
        $s->expects($this->exactly(2))
            ->method('read')
            ->will($this->returnCallback(function () {
                static $c = false;
                if ($c) {
                    return false;
                }
                $c = true;
                return 'h';
            }));
        $s->expects($this->exactly(2))
            ->method('eof')
            ->will($this->returnValue(false));
        $this->assertEquals("h", Utils::readline($s));
    }

    public function testCalculatesHash()
    {
        $s = Stream::factory('foobazbar');
        $this->assertEquals(md5('foobazbar'), Utils::hash($s, 'md5'));
    }

    /**
     * @expectedException \GuzzleHttp\Stream\Exception\SeekException
     */
    public function testCalculatesHashThrowsWhenSeekFails()
    {
        $s = new NoSeekStream(Stream::factory('foobazbar'));
        $s->read(2);
        Utils::hash($s, 'md5');
    }

    public function testCalculatesHashSeeksToOriginalPosition()
    {
        $s = Stream::factory('foobazbar');
        $s->seek(4);
        $this->assertEquals(md5('foobazbar'), Utils::hash($s, 'md5'));
        $this->assertEquals(4, $s->tell());
    }

    public function testOpensFilesSuccessfully()
    {
        $r = Utils::open(__FILE__, 'r');
        $this->assertInternalType('resource', $r);
        fclose($r);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to open /path/to/does/not/exist using mode r
     */
    public function testThrowsExceptionNotWarning()
    {
        Utils::open('/path/to/does/not/exist', 'r');
    }

    public function testProxiesToFactory()
    {
        $this->assertEquals('foo', (string) Utils::create('foo'));
    }
}
