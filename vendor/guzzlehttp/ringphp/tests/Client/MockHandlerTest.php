<?php
namespace GuzzleHttp\Tests\Ring\Client;

use GuzzleHttp\Ring\Client\MockHandler;
use GuzzleHttp\Ring\Future\FutureArray;
use React\Promise\Deferred;

class MockHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsArray()
    {
        $mock = new MockHandler(['status' => 200]);
        $response = $mock([]);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals([], $response['headers']);
        $this->assertNull($response['body']);
        $this->assertNull($response['reason']);
        $this->assertNull($response['effective_url']);
    }

    public function testReturnsFutures()
    {
        $deferred = new Deferred();
        $future = new FutureArray(
            $deferred->promise(),
            function () use ($deferred) {
                $deferred->resolve(['status' => 200]);
            }
        );
        $mock = new MockHandler($future);
        $response = $mock([]);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $response);
        $this->assertEquals(200, $response['status']);
    }

    public function testReturnsFuturesWithThenCall()
    {
        $deferred = new Deferred();
        $future = new FutureArray(
            $deferred->promise(),
            function () use ($deferred) {
                $deferred->resolve(['status' => 200]);
            }
        );
        $mock = new MockHandler($future);
        $response = $mock([]);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $response);
        $this->assertEquals(200, $response['status']);
        $req = null;
        $promise = $response->then(function ($value) use (&$req) {
            $req = $value;
            $this->assertEquals(200, $req['status']);
        });
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);
        $this->assertEquals(200, $req['status']);
    }

    public function testReturnsFuturesAndProxiesCancel()
    {
        $c = null;
        $deferred = new Deferred();
        $future = new FutureArray(
            $deferred->promise(),
            function () {},
            function () use (&$c) {
                $c = true;
                return true;
            }
        );
        $mock = new MockHandler($future);
        $response = $mock([]);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $response);
        $response->cancel();
        $this->assertTrue($c);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Response must be an array or FutureArrayInterface. Found
     */
    public function testEnsuresMockIsValid()
    {
        $mock = new MockHandler('foo');
        $mock([]);
    }
}
