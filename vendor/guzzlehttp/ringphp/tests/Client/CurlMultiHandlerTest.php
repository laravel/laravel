<?php
namespace GuzzleHttp\Tests\Ring\Client;

use GuzzleHttp\Ring\Client\CurlMultiHandler;

class CurlMultiHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testSendsRequest()
    {
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
        ]);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('transfer_stats', $response);
        $realUrl = trim($response['transfer_stats']['url'], '/');
        $this->assertEquals(trim(Server::$url, '/'), $realUrl);
        $this->assertArrayHasKey('effective_url', $response);
        $this->assertEquals(
            trim(Server::$url, '/'),
            trim($response['effective_url'], '/')
        );
    }

    public function testCreatesErrorResponses()
    {
        $url = 'http://localhost:123/';
        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'GET',
            'headers'     => ['host' => ['localhost:123']],
        ]);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $response);
        $this->assertNull($response['status']);
        $this->assertNull($response['reason']);
        $this->assertEquals([], $response['headers']);
        $this->assertArrayHasKey('error', $response);
        $this->assertContains('cURL error ', $response['error']->getMessage());
        $this->assertArrayHasKey('transfer_stats', $response);
        $this->assertEquals(
            trim($url, '/'),
            trim($response['transfer_stats']['url'], '/')
        );
        $this->assertArrayHasKey('effective_url', $response);
        $this->assertEquals(
            trim($url, '/'),
            trim($response['effective_url'], '/')
        );
    }

    public function testSendsFuturesWhenDestructed()
    {
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
        ]);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $response);
        $a->__destruct();
        $this->assertEquals(200, $response['status']);
    }

    public function testCanSetMaxHandles()
    {
        $a = new CurlMultiHandler(['max_handles' => 2]);
        $this->assertEquals(2, $this->readAttribute($a, 'maxHandles'));
    }

    public function testCanSetSelectTimeout()
    {
        $a = new CurlMultiHandler(['select_timeout' => 2]);
        $this->assertEquals(2, $this->readAttribute($a, 'selectTimeout'));
    }

    public function testSendsFuturesWhenMaxHandlesIsReached()
    {
        $request = [
            'http_method' => 'PUT',
            'headers'     => ['host' => [Server::$host]],
            'future'      => 'lazy', // passing this to control the test
        ];
        $response = ['status' => 200];
        Server::flush();
        Server::enqueue([$response, $response, $response]);
        $a = new CurlMultiHandler(['max_handles' => 3]);
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $a($request);
        }
        $this->assertCount(3, Server::received());
        $responses[3]->cancel();
        $responses[4]->cancel();
    }

    public function testCanCancel()
    {
        Server::flush();
        $response = ['status' => 200];
        Server::enqueue(array_fill_keys(range(0, 10), $response));
        $a = new CurlMultiHandler();
        $responses = [];

        for ($i = 0; $i < 10; $i++) {
            $response = $a([
                'http_method' => 'GET',
                'headers'     => ['host' => [Server::$host]],
                'future'      => 'lazy',
            ]);
            $response->cancel();
            $responses[] = $response;
        }

        $this->assertCount(0, Server::received());

        foreach ($responses as $response) {
            $this->assertTrue($this->readAttribute($response, 'isRealized'));
        }
    }

    public function testCannotCancelFinished()
    {
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
        ]);
        $response->wait();
        $response->cancel();
    }

    public function testDelaysInParallel()
    {
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $expected = microtime(true) + (100 / 1000);
        $response = $a([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client'      => ['delay' => 100],
        ]);
        $response->wait();
        $this->assertGreaterThanOrEqual($expected, microtime(true));
    }

    public function testSendsNonLazyFutures()
    {
        $request = [
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'future'      => true,
        ];
        Server::flush();
        Server::enqueue([['status' => 202]]);
        $a = new CurlMultiHandler();
        $response = $a($request);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $response);
        $this->assertEquals(202, $response['status']);
    }
}
