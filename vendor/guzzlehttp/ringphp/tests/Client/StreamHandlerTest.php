<?php
namespace GuzzleHttp\Tests\Ring\Client;

use GuzzleHttp\Ring\Client\ClientUtils;
use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Client\StreamHandler;

class StreamHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsResponseForSuccessfulRequest()
    {
        $this->queueRes();
        $handler = new StreamHandler();
        $response = $handler([
            'http_method' => 'GET',
            'uri'         => '/',
            'headers'     => [
                'host' => [Server::$host],
                'Foo' => ['Bar'],
            ],
        ]);

        $this->assertEquals('1.1', $response['version']);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('OK', $response['reason']);
        $this->assertEquals(['Bar'], $response['headers']['Foo']);
        $this->assertEquals(['8'], $response['headers']['Content-Length']);
        $this->assertEquals('hi there', Core::body($response));

        $sent = Server::received()[0];
        $this->assertEquals('GET', $sent['http_method']);
        $this->assertEquals('/', $sent['resource']);
        $this->assertEquals(['127.0.0.1:8125'], $sent['headers']['host']);
        $this->assertEquals('Bar', Core::header($sent, 'foo'));
    }

    public function testAddsErrorToResponse()
    {
        $handler = new StreamHandler();
        $result = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => ['localhost:123']],
            'client'      => ['timeout' => 0.01],
        ]);
        $this->assertInstanceOf(
            'GuzzleHttp\Ring\Future\CompletedFutureArray',
            $result
        );
        $this->assertNull($result['status']);
        $this->assertNull($result['body']);
        $this->assertEquals([], $result['headers']);
        $this->assertInstanceOf(
            'GuzzleHttp\Ring\Exception\RingException',
            $result['error']
        );
    }

    public function testEnsuresTheHttpProtocol()
    {
        $handler = new StreamHandler();
        $result = $handler([
            'http_method' => 'GET',
            'url'         => 'ftp://localhost:123',
        ]);
        $this->assertArrayHasKey('error', $result);
        $this->assertContains(
            'URL is invalid: ftp://localhost:123',
            $result['error']->getMessage()
        );
    }

    public function testStreamAttributeKeepsStreamOpen()
    {
        $this->queueRes();
        $handler = new StreamHandler();
        $response = $handler([
            'http_method'  => 'PUT',
            'uri'          => '/foo',
            'query_string' => 'baz=bar',
            'headers'      => [
                'host' => [Server::$host],
                'Foo'  => ['Bar'],
            ],
            'body'         => 'test',
            'client'       => ['stream' => true],
        ]);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('OK', $response['reason']);
        $this->assertEquals('8', Core::header($response, 'Content-Length'));
        $body = $response['body'];
        $this->assertTrue(is_resource($body));
        $this->assertEquals('http', stream_get_meta_data($body)['wrapper_type']);
        $this->assertEquals('hi there', stream_get_contents($body));
        fclose($body);
        $sent = Server::received()[0];
        $this->assertEquals('PUT', $sent['http_method']);
        $this->assertEquals('/foo', $sent['uri']);
        $this->assertEquals('baz=bar', $sent['query_string']);
        $this->assertEquals('/foo?baz=bar', $sent['resource']);
        $this->assertEquals('127.0.0.1:8125', Core::header($sent, 'host'));
        $this->assertEquals('Bar', Core::header($sent, 'foo'));
    }

    public function testDrainsResponseIntoTempStream()
    {
        $this->queueRes();
        $handler = new StreamHandler();
        $response = $handler([
            'http_method' => 'GET',
            'uri'         => '/',
            'headers'     => ['host' => [Server::$host]],
        ]);
        $body = $response['body'];
        $this->assertEquals('php://temp', stream_get_meta_data($body)['uri']);
        $this->assertEquals('hi', fread($body, 2));
        fclose($body);
    }

    public function testDrainsResponseIntoSaveToBody()
    {
        $r = fopen('php://temp', 'r+');
        $this->queueRes();
        $handler = new StreamHandler();
        $response = $handler([
            'http_method' => 'GET',
            'uri' => '/',
            'headers' => ['host' => [Server::$host]],
            'client' => ['save_to' => $r],
        ]);
        $body = $response['body'];
        $this->assertEquals('php://temp', stream_get_meta_data($body)['uri']);
        $this->assertEquals('hi', fread($body, 2));
        $this->assertEquals(' there', stream_get_contents($r));
        fclose($r);
    }

    public function testDrainsResponseIntoSaveToBodyAtPath()
    {
        $tmpfname = tempnam('/tmp', 'save_to_path');
        $this->queueRes();
        $handler = new StreamHandler();
        $response = $handler([
            'http_method' => 'GET',
            'uri' => '/',
            'headers' => ['host' => [Server::$host]],
            'client' => ['save_to' => $tmpfname],
        ]);
        $body = $response['body'];
        $this->assertInstanceOf('GuzzleHttp\Stream\StreamInterface', $body);
        $this->assertEquals($tmpfname, $body->getMetadata('uri'));
        $this->assertEquals('hi', $body->read(2));
        $body->close();
        unlink($tmpfname);
    }

    public function testAutomaticallyDecompressGzip()
    {
        Server::flush();
        $content = gzencode('test');
        Server::enqueue([
            [
                'status' => 200,
                'reason' => 'OK',
                'headers' => [
                    'Content-Encoding' => ['gzip'],
                    'Content-Length' => [strlen($content)],
                ],
                'body' => $content,
            ],
        ]);

        $handler = new StreamHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'uri'         => '/',
            'client'      => ['decode_content' => true],
        ]);
        $this->assertEquals('test', Core::body($response));
    }

    public function testDoesNotForceGzipDecode()
    {
        Server::flush();
        $content = gzencode('test');
        Server::enqueue([
            [
                'status' => 200,
                'reason' => 'OK',
                'headers' => [
                    'Content-Encoding' => ['gzip'],
                    'Content-Length'   => [strlen($content)],
                ],
                'body' => $content,
            ],
        ]);

        $handler = new StreamHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'uri'         => '/',
            'client'      => ['stream' => true, 'decode_content' => false],
        ]);
        $this->assertSame($content, Core::body($response));
    }

    public function testProtocolVersion()
    {
        $this->queueRes();
        $handler = new StreamHandler();
        $handler([
            'http_method' => 'GET',
            'uri'         => '/',
            'headers'     => ['host' => [Server::$host]],
            'version'     => 1.0,
        ]);

        $this->assertEquals(1.0, Server::received()[0]['version']);
    }

    protected function getSendResult(array $opts)
    {
        $this->queueRes();
        $handler = new StreamHandler();
        $opts['stream'] = true;
        return $handler([
            'http_method' => 'GET',
            'uri'         => '/',
            'headers'     => ['host' => [Server::$host]],
            'client'      => $opts,
        ]);
    }

    public function testAddsProxy()
    {
        $res = $this->getSendResult(['stream' => true, 'proxy' => '127.0.0.1:8125']);
        $opts = stream_context_get_options($res['body']);
        $this->assertEquals('127.0.0.1:8125', $opts['http']['proxy']);
    }

    public function testAddsTimeout()
    {
        $res = $this->getSendResult(['stream' => true, 'timeout' => 200]);
        $opts = stream_context_get_options($res['body']);
        $this->assertEquals(200, $opts['http']['timeout']);
    }

    public function testVerifiesVerifyIsValidIfPath()
    {
        $res = $this->getSendResult(['verify' => '/does/not/exist']);
        $this->assertContains(
            'SSL CA bundle not found: /does/not/exist',
            (string) $res['error']
        );
    }

    public function testVerifyCanBeDisabled()
    {
        $res = $this->getSendResult(['verify' => false]);
        $this->assertArrayNotHasKey('error', $res);
    }

    public function testVerifiesCertIfValidPath()
    {
        $res = $this->getSendResult(['cert' => '/does/not/exist']);
        $this->assertContains(
            'SSL certificate not found: /does/not/exist',
            (string) $res['error']
        );
    }

    public function testVerifyCanBeSetToPath()
    {
        $path = $path = ClientUtils::getDefaultCaBundle();
        $res = $this->getSendResult(['verify' => $path]);
        $this->assertArrayNotHasKey('error', $res);
        $opts = stream_context_get_options($res['body']);
        $this->assertEquals(true, $opts['ssl']['verify_peer']);
        $this->assertEquals($path, $opts['ssl']['cafile']);
        $this->assertTrue(file_exists($opts['ssl']['cafile']));
    }

    public function testUsesSystemDefaultBundle()
    {
        $path = $path = ClientUtils::getDefaultCaBundle();
        $res = $this->getSendResult(['verify' => true]);
        $this->assertArrayNotHasKey('error', $res);
        $opts = stream_context_get_options($res['body']);
        if (PHP_VERSION_ID < 50600) {
            $this->assertEquals($path, $opts['ssl']['cafile']);
        }
    }

    public function testEnsuresVerifyOptionIsValid()
    {
        $res = $this->getSendResult(['verify' => 10]);
        $this->assertContains(
            'Invalid verify request option',
            (string) $res['error']
        );
    }

    public function testCanSetPasswordWhenSettingCert()
    {
        $path = __FILE__;
        $res = $this->getSendResult(['cert' => [$path, 'foo']]);
        $opts = stream_context_get_options($res['body']);
        $this->assertEquals($path, $opts['ssl']['local_cert']);
        $this->assertEquals('foo', $opts['ssl']['passphrase']);
    }

    public function testDebugAttributeWritesToStream()
    {
        $this->queueRes();
        $f = fopen('php://temp', 'w+');
        $this->getSendResult(['debug' => $f]);
        fseek($f, 0);
        $contents = stream_get_contents($f);
        $this->assertContains('<GET http://127.0.0.1:8125/> [CONNECT]', $contents);
        $this->assertContains('<GET http://127.0.0.1:8125/> [FILE_SIZE_IS]', $contents);
        $this->assertContains('<GET http://127.0.0.1:8125/> [PROGRESS]', $contents);
    }

    public function testDebugAttributeWritesStreamInfoToBuffer()
    {
        $called = false;
        $this->queueRes();
        $buffer = fopen('php://temp', 'r+');
        $this->getSendResult([
            'progress' => function () use (&$called) { $called = true; },
            'debug' => $buffer,
        ]);
        fseek($buffer, 0);
        $contents = stream_get_contents($buffer);
        $this->assertContains('<GET http://127.0.0.1:8125/> [CONNECT]', $contents);
        $this->assertContains('<GET http://127.0.0.1:8125/> [FILE_SIZE_IS] message: "Content-Length: 8"', $contents);
        $this->assertContains('<GET http://127.0.0.1:8125/> [PROGRESS] bytes_max: "8"', $contents);
        $this->assertTrue($called);
    }

    public function testEmitsProgressInformation()
    {
        $called = [];
        $this->queueRes();
        $this->getSendResult([
            'progress' => function () use (&$called) {
                $called[] = func_get_args();
            },
        ]);
        $this->assertNotEmpty($called);
        $this->assertEquals(8, $called[0][0]);
        $this->assertEquals(0, $called[0][1]);
    }

    public function testEmitsProgressInformationAndDebugInformation()
    {
        $called = [];
        $this->queueRes();
        $buffer = fopen('php://memory', 'w+');
        $this->getSendResult([
            'debug'    => $buffer,
            'progress' => function () use (&$called) {
                $called[] = func_get_args();
            },
        ]);
        $this->assertNotEmpty($called);
        $this->assertEquals(8, $called[0][0]);
        $this->assertEquals(0, $called[0][1]);
        rewind($buffer);
        $this->assertNotEmpty(stream_get_contents($buffer));
        fclose($buffer);
    }

    public function testAddsProxyByProtocol()
    {
        $url = str_replace('http', 'tcp', Server::$url);
        $res = $this->getSendResult(['proxy' => ['http' => $url]]);
        $opts = stream_context_get_options($res['body']);
        $this->assertEquals($url, $opts['http']['proxy']);
    }

    public function testPerformsShallowMergeOfCustomContextOptions()
    {
        $res = $this->getSendResult([
            'stream_context' => [
                'http' => [
                    'request_fulluri' => true,
                    'method' => 'HEAD',
                ],
                'socket' => [
                    'bindto' => '127.0.0.1:0',
                ],
                'ssl' => [
                    'verify_peer' => false,
                ],
            ],
        ]);

        $opts = stream_context_get_options($res['body']);
        $this->assertEquals('HEAD', $opts['http']['method']);
        $this->assertTrue($opts['http']['request_fulluri']);
        $this->assertFalse($opts['ssl']['verify_peer']);
        $this->assertEquals('127.0.0.1:0', $opts['socket']['bindto']);
    }

    public function testEnsuresThatStreamContextIsAnArray()
    {
        $res = $this->getSendResult(['stream_context' => 'foo']);
        $this->assertContains(
            'stream_context must be an array',
            (string) $res['error']
        );
    }

    public function testDoesNotAddContentTypeByDefault()
    {
        $this->queueRes();
        $handler = new StreamHandler();
        $handler([
            'http_method' => 'PUT',
            'uri' => '/',
            'headers' => ['host' => [Server::$host], 'content-length' => [3]],
            'body' => 'foo',
        ]);
        $req = Server::received()[0];
        $this->assertEquals('', Core::header($req, 'Content-Type'));
        $this->assertEquals(3, Core::header($req, 'Content-Length'));
    }

    private function queueRes()
    {
        Server::flush();
        Server::enqueue([
            [
                'status' => 200,
                'reason' => 'OK',
                'headers' => [
                    'Foo' => ['Bar'],
                    'Content-Length' => [8],
                ],
                'body' => 'hi there',
            ],
        ]);
    }

    public function testSupports100Continue()
    {
        Server::flush();
        Server::enqueue([
            [
                'status' => '200',
                'reason' => 'OK',
                'headers' => [
                    'Test' => ['Hello'],
                    'Content-Length' => ['4'],
                ],
                'body' => 'test',
            ],
        ]);

        $request = [
            'http_method' => 'PUT',
            'headers'     => [
                'Host'   => [Server::$host],
                'Expect' => ['100-Continue'],
            ],
            'body'        => 'test',
        ];

        $handler = new StreamHandler();
        $response = $handler($request);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('OK', $response['reason']);
        $this->assertEquals(['Hello'], $response['headers']['Test']);
        $this->assertEquals(['4'], $response['headers']['Content-Length']);
        $this->assertEquals('test', Core::body($response));
    }
}
