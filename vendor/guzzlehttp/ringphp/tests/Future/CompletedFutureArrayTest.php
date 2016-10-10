<?php
namespace GuzzleHttp\Tests\Ring\Future;

use GuzzleHttp\Ring\Future\CompletedFutureArray;

class CompletedFutureArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsAsArray()
    {
        $f = new CompletedFutureArray(['foo' => 'bar']);
        $this->assertEquals('bar', $f['foo']);
        $this->assertFalse(isset($f['baz']));
        $f['abc'] = '123';
        $this->assertTrue(isset($f['abc']));
        $this->assertEquals(['foo' => 'bar', 'abc' => '123'], iterator_to_array($f));
        $this->assertEquals(2, count($f));
        unset($f['abc']);
        $this->assertEquals(1, count($f));
        $this->assertEquals(['foo' => 'bar'], iterator_to_array($f));
    }
}
