<?php
namespace GuzzleHttp\Tests\Stream;

use GuzzleHttp\Stream\InflateStream;
use GuzzleHttp\Stream\Stream;

class InflateStreamtest extends \PHPUnit_Framework_TestCase
{
    public function testInflatesStreams()
    {
        $content = gzencode('test');
        $a = Stream::factory($content);
        $b = new InflateStream($a);
        $this->assertEquals('test', (string) $b);
    }
}
