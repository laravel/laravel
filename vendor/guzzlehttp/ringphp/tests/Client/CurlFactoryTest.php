<?php
// Override curl_setopt_array() to get the last set curl options
namespace GuzzleHttp\Ring\Client {
    function curl_setopt_array($handle, array $options) {
        if (!empty($_SERVER['curl_test'])) {
            $_SERVER['_curl'] = $options;
        } else {
            unset($_SERVER['_curl']);
        }
        \curl_setopt_array($handle, $options);
    }
}

namespace GuzzleHttp\Tests\Ring\Client {

use GuzzleHttp\Ring\Client\CurlFactory;
use GuzzleHttp\Ring\Client\CurlMultiHandler;
use GuzzleHttp\Ring\Client\MockHandler;
use GuzzleHttp\Ring\Core;
use GuzzleHttp\Stream\FnStream;
use GuzzleHttp\Stream\NoSeekStream;
use GuzzleHttp\Stream\Stream;

class CurlFactoryTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $_SERVER['curl_test'] = true;
        unset($_SERVER['_curl']);
    }

    public static function tearDownAfterClass()
    {
        unset($_SERVER['_curl'], $_SERVER['curl_test']);
    }

    public function testCreatesCurlHandle()
    {
        Server::flush();
        Server::enqueue([[
            'status' => 200,
            'headers' => [
                'Foo' => ['Bar'],
                'Baz' => ['bam'],
                'Content-Length' => [2],
            ],
            'body' => 'hi',
        ]]);

        $stream = Stream::factory();

        $request = [
            'http_method' => 'PUT',
            'headers' => [
                'host' => [Server::$url],
                'Hi'   => [' 123'],
            ],
            'body' => 'testing',
            'client' => ['save_to' => $stream],
        ];

        $f = new CurlFactory();
        $result = $f($request);
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertInternalType('resource', $result[0]);
        $this->assertInternalType('array', $result[1]);
        $this->assertSame($stream, $result[2]);
        curl_close($result[0]);

        $this->assertEquals('PUT', $_SERVER['_curl'][CURLOPT_CUSTOMREQUEST]);
        $this->assertEquals(
            'http://http://127.0.0.1:8125/',
            $_SERVER['_curl'][CURLOPT_URL]
        );
        // Sends via post fields when the request is small enough
        $this->assertEquals('testing', $_SERVER['_curl'][CURLOPT_POSTFIELDS]);
        $this->assertEquals(0, $_SERVER['_curl'][CURLOPT_RETURNTRANSFER]);
        $this->assertEquals(0, $_SERVER['_curl'][CURLOPT_HEADER]);
        $this->assertEquals(150, $_SERVER['_curl'][CURLOPT_CONNECTTIMEOUT]);
        $this->assertInstanceOf('Closure', $_SERVER['_curl'][CURLOPT_HEADERFUNCTION]);

        if (defined('CURLOPT_PROTOCOLS')) {
            $this->assertEquals(
                CURLPROTO_HTTP | CURLPROTO_HTTPS,
                $_SERVER['_curl'][CURLOPT_PROTOCOLS]
            );
        }

        $this->assertContains('Expect:', $_SERVER['_curl'][CURLOPT_HTTPHEADER]);
        $this->assertContains('Accept:', $_SERVER['_curl'][CURLOPT_HTTPHEADER]);
        $this->assertContains('Content-Type:', $_SERVER['_curl'][CURLOPT_HTTPHEADER]);
        $this->assertContains('Hi:  123', $_SERVER['_curl'][CURLOPT_HTTPHEADER]);
        $this->assertContains('host: http://127.0.0.1:8125/', $_SERVER['_curl'][CURLOPT_HTTPHEADER]);
    }

    public function testSendsHeadRequests()
    {
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'HEAD',
            'headers' => ['host' => [Server::$host]],
        ]);
        $response->wait();
        $this->assertEquals(true, $_SERVER['_curl'][CURLOPT_NOBODY]);
        $checks = [CURLOPT_WRITEFUNCTION, CURLOPT_READFUNCTION, CURLOPT_FILE, CURLOPT_INFILE];
        foreach ($checks as $check) {
            $this->assertArrayNotHasKey($check, $_SERVER['_curl']);
        }
        $this->assertEquals('HEAD', Server::received()[0]['http_method']);
    }

    public function testCanAddCustomCurlOptions()
    {
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $a([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client'      => ['curl' => [CURLOPT_LOW_SPEED_LIMIT => 10]],
        ]);
        $this->assertEquals(10, $_SERVER['_curl'][CURLOPT_LOW_SPEED_LIMIT]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage SSL CA bundle not found: /does/not/exist
     */
    public function testValidatesVerify()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['verify' => '/does/not/exist'],
        ]);
    }

    public function testCanSetVerifyToFile()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['verify' => __FILE__],
        ]);
        $this->assertEquals(__FILE__, $_SERVER['_curl'][CURLOPT_CAINFO]);
        $this->assertEquals(2, $_SERVER['_curl'][CURLOPT_SSL_VERIFYHOST]);
        $this->assertEquals(true, $_SERVER['_curl'][CURLOPT_SSL_VERIFYPEER]);
    }

    public function testAddsVerifyAsTrue()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['verify' => true],
        ]);
        $this->assertEquals(2, $_SERVER['_curl'][CURLOPT_SSL_VERIFYHOST]);
        $this->assertEquals(true, $_SERVER['_curl'][CURLOPT_SSL_VERIFYPEER]);
        $this->assertArrayNotHasKey(CURLOPT_CAINFO, $_SERVER['_curl']);
    }

    public function testCanDisableVerify()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['verify' => false],
        ]);
        $this->assertEquals(0, $_SERVER['_curl'][CURLOPT_SSL_VERIFYHOST]);
        $this->assertEquals(false, $_SERVER['_curl'][CURLOPT_SSL_VERIFYPEER]);
    }

    public function testAddsProxy()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['proxy' => 'http://bar.com'],
        ]);
        $this->assertEquals('http://bar.com', $_SERVER['_curl'][CURLOPT_PROXY]);
    }

    public function testAddsViaScheme()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'scheme' => 'http',
            'headers' => ['host' => ['foo.com']],
            'client' => [
                'proxy' => ['http' => 'http://bar.com', 'https' => 'https://t'],
            ],
        ]);
        $this->assertEquals('http://bar.com', $_SERVER['_curl'][CURLOPT_PROXY]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage SSL private key not found: /does/not/exist
     */
    public function testValidatesSslKey()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['ssl_key' => '/does/not/exist'],
        ]);
    }

    public function testAddsSslKey()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['ssl_key' => __FILE__],
        ]);
        $this->assertEquals(__FILE__, $_SERVER['_curl'][CURLOPT_SSLKEY]);
    }

    public function testAddsSslKeyWithPassword()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['ssl_key' => [__FILE__, 'test']],
        ]);
        $this->assertEquals(__FILE__, $_SERVER['_curl'][CURLOPT_SSLKEY]);
        $this->assertEquals('test', $_SERVER['_curl'][CURLOPT_SSLKEYPASSWD]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage SSL certificate not found: /does/not/exist
     */
    public function testValidatesCert()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['cert' => '/does/not/exist'],
        ]);
    }

    public function testAddsCert()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['cert' => __FILE__],
        ]);
        $this->assertEquals(__FILE__, $_SERVER['_curl'][CURLOPT_SSLCERT]);
    }

    public function testAddsCertWithPassword()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['cert' => [__FILE__, 'test']],
        ]);
        $this->assertEquals(__FILE__, $_SERVER['_curl'][CURLOPT_SSLCERT]);
        $this->assertEquals('test', $_SERVER['_curl'][CURLOPT_SSLCERTPASSWD]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage progress client option must be callable
     */
    public function testValidatesProgress()
    {
        $f = new CurlFactory();
        $f([
            'http_method' => 'GET',
            'headers' => ['host' => ['foo.com']],
            'client' => ['progress' => 'foo'],
        ]);
    }

    public function testEmitsDebugInfoToStream()
    {
        $res = fopen('php://memory', 'r+');
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'HEAD',
            'headers'     => ['host' => [Server::$host]],
            'client'      => ['debug' => $res],
        ]);
        $response->wait();
        rewind($res);
        $output = str_replace("\r", '', stream_get_contents($res));
        $this->assertContains(
            "> HEAD / HTTP/1.1\nhost: 127.0.0.1:8125\n\n",
            $output
        );
        $this->assertContains("< HTTP/1.1 200", $output);
        fclose($res);
    }

    public function testEmitsProgressToFunction()
    {
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $called = [];
        $response = $a([
            'http_method' => 'HEAD',
            'headers'     => ['host' => [Server::$host]],
            'client'      => [
                'progress' => function () use (&$called) {
                    $called[] = func_get_args();
                },
            ],
        ]);
        $response->wait();
        $this->assertNotEmpty($called);
        foreach ($called as $call) {
            $this->assertCount(4, $call);
        }
    }

    private function addDecodeResponse($withEncoding = true)
    {
        $content = gzencode('test');
        $response  = [
            'status'  => 200,
            'reason'  => 'OK',
            'headers' => ['Content-Length' => [strlen($content)]],
            'body'    => $content,
        ];

        if ($withEncoding) {
            $response['headers']['Content-Encoding'] = ['gzip'];
        }

        Server::flush();
        Server::enqueue([$response]);

        return $content;
    }

    public function testDecodesGzippedResponses()
    {
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client'      => ['decode_content' => true],
        ]);
        $response->wait();
        $this->assertEquals('test', Core::body($response));
        $this->assertEquals('', $_SERVER['_curl'][CURLOPT_ENCODING]);
        $sent = Server::received()[0];
        $this->assertNull(Core::header($sent, 'Accept-Encoding'));
    }

    public function testDecodesGzippedResponsesWithHeader()
    {
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => [
                'host'            => [Server::$host],
                'Accept-Encoding' => ['gzip'],
            ],
            'client' => ['decode_content' => true],
        ]);
        $response->wait();
        $this->assertEquals('gzip', $_SERVER['_curl'][CURLOPT_ENCODING]);
        $sent = Server::received()[0];
        $this->assertEquals('gzip', Core::header($sent, 'Accept-Encoding'));
        $this->assertEquals('test', Core::body($response));
    }

    public function testDoesNotForceDecode()
    {
        $content = $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client'      => ['decode_content' => false],
        ]);
        $response->wait();
        $sent = Server::received()[0];
        $this->assertNull(Core::header($sent, 'Accept-Encoding'));
        $this->assertEquals($content, Core::body($response));
    }

    public function testProtocolVersion()
    {
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $a = new CurlMultiHandler();
        $a([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'version'     => 1.0,
        ]);
        $this->assertEquals(CURL_HTTP_VERSION_1_0, $_SERVER['_curl'][CURLOPT_HTTP_VERSION]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidatesSaveTo()
    {
        $handler = new CurlMultiHandler();
        $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client'      => ['save_to' => true],
        ]);
    }

    public function testSavesToStream()
    {
        $stream = fopen('php://memory', 'r+');
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client' => [
                'decode_content' => true,
                'save_to' => $stream,
            ],
        ]);
        $response->wait();
        rewind($stream);
        $this->assertEquals('test', stream_get_contents($stream));
    }

    public function testSavesToGuzzleStream()
    {
        $stream = Stream::factory();
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client' => [
                'decode_content' => true,
                'save_to'        => $stream,
            ],
        ]);
        $response->wait();
        $this->assertEquals('test', (string) $stream);
    }

    public function testSavesToFileOnDisk()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'testfile');
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'client' => [
                'decode_content' => true,
                'save_to'        => $tmpfile,
            ],
        ]);
        $response->wait();
        $this->assertEquals('test', file_get_contents($tmpfile));
        unlink($tmpfile);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidatesBody()
    {
        $handler = new CurlMultiHandler();
        $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'body'        => false,
        ]);
    }

    public function testAddsLargePayloadFromStreamWithNoSizeUsingChunked()
    {
        $stream = Stream::factory('foo');
        $stream = FnStream::decorate($stream, [
            'getSize' => function () {
                return null;
            }
        ]);
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'body'        => $stream,
        ]);
        $response->wait();
        $sent = Server::received()[0];
        $this->assertEquals('chunked', Core::header($sent, 'Transfer-Encoding'));
        $this->assertNull(Core::header($sent, 'Content-Length'));
        $this->assertEquals('foo', $sent['body']);
    }

    public function testAddsPayloadFromIterator()
    {
        $iter = new \ArrayIterator(['f', 'o', 'o']);
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'body'        => $iter,
        ]);
        $response->wait();
        $sent = Server::received()[0];
        $this->assertEquals('chunked', Core::header($sent, 'Transfer-Encoding'));
        $this->assertNull(Core::header($sent, 'Content-Length'));
        $this->assertEquals('foo', $sent['body']);
    }

    public function testAddsPayloadFromResource()
    {
        $res = fopen('php://memory', 'r+');
        $data = str_repeat('.', 1000000);
        fwrite($res, $data);
        rewind($res);
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => [
                'host'           => [Server::$host],
                'content-length' => [1000000],
            ],
            'body'        => $res,
        ]);
        $response->wait();
        $sent = Server::received()[0];
        $this->assertNull(Core::header($sent, 'Transfer-Encoding'));
        $this->assertEquals(1000000, Core::header($sent, 'Content-Length'));
        $this->assertEquals($data, $sent['body']);
    }

    public function testAddsContentLengthFromStream()
    {
        $stream = Stream::factory('foo');
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => ['host' => [Server::$host]],
            'body'        => $stream,
        ]);
        $response->wait();
        $sent = Server::received()[0];
        $this->assertEquals(3, Core::header($sent, 'Content-Length'));
        $this->assertNull(Core::header($sent, 'Transfer-Encoding'));
        $this->assertEquals('foo', $sent['body']);
    }

    public function testDoesNotAddMultipleContentLengthHeaders()
    {
        $this->addDecodeResponse();
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'GET',
            'headers'     => [
                'host'           => [Server::$host],
                'content-length' => [3],
            ],
            'body' => 'foo',
        ]);
        $response->wait();
        $sent = Server::received()[0];
        $this->assertEquals(3, Core::header($sent, 'Content-Length'));
        $this->assertNull(Core::header($sent, 'Transfer-Encoding'));
        $this->assertEquals('foo', $sent['body']);
    }

    public function testSendsPostWithNoBodyOrDefaultContentType()
    {
        Server::flush();
        Server::enqueue([['status' => 200]]);
        $handler = new CurlMultiHandler();
        $response = $handler([
            'http_method' => 'POST',
            'uri'         => '/',
            'headers'     => ['host' => [Server::$host]],
        ]);
        $response->wait();
        $received = Server::received()[0];
        $this->assertEquals('POST', $received['http_method']);
        $this->assertNull(Core::header($received, 'content-type'));
        $this->assertSame('0', Core::firstHeader($received, 'content-length'));
    }

    public function testParseProtocolVersion()
    {
        $res = CurlFactory::createResponse(
            function () {},
            [],
            ['curl' => ['errno' => null]],
            ['HTTP/1.1 200 Ok'],
            null
        );

        $this->assertSame('1.1', $res['version']);
    }

    public function testFailsWhenNoResponseAndNoBody()
    {
        $res = CurlFactory::createResponse(function () {}, [], [], [], null);
        $this->assertInstanceOf('GuzzleHttp\Ring\Exception\RingException', $res['error']);
        $this->assertContains(
            'No response was received for a request with no body',
            $res['error']->getMessage()
        );
    }

    public function testFailsWhenCannotRewindRetry()
    {
        $res = CurlFactory::createResponse(function () {}, [
            'body' => new NoSeekStream(Stream::factory('foo'))
        ], [], [], null);
        $this->assertInstanceOf('GuzzleHttp\Ring\Exception\RingException', $res['error']);
        $this->assertContains(
            'rewind the request body failed',
            $res['error']->getMessage()
        );
    }

    public function testRetriesWhenBodyCanBeRewound()
    {
        $callHandler = $called = false;
        $res = CurlFactory::createResponse(function () use (&$callHandler) {
            $callHandler = true;
            return ['status' => 200];
        }, [
            'body' => FnStream::decorate(Stream::factory('test'), [
                'seek' => function () use (&$called) {
                    $called = true;
                    return true;
                }
            ])
        ], [], [], null);

        $this->assertTrue($callHandler);
        $this->assertTrue($called);
        $this->assertEquals('200', $res['status']);
    }

    public function testFailsWhenRetryMoreThanThreeTimes()
    {
        $call = 0;
        $mock = new MockHandler(function (array $request) use (&$mock, &$call) {
            $call++;
            return CurlFactory::createResponse($mock, $request, [], [], null);
        });
        $response = $mock([
            'http_method' => 'GET',
            'body'        => 'test',
        ]);
        $this->assertEquals(3, $call);
        $this->assertArrayHasKey('error', $response);
        $this->assertContains(
            'The cURL request was retried 3 times',
            $response['error']->getMessage()
        );
    }

    public function testHandles100Continue()
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

        $handler = new CurlMultiHandler();
        $response = $handler($request)->wait();
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('OK', $response['reason']);
        $this->assertEquals(['Hello'], $response['headers']['Test']);
        $this->assertEquals(['4'], $response['headers']['Content-Length']);
        $this->assertEquals('test', Core::body($response));
    }

    public function testCreatesConnectException()
    {
        $m = new \ReflectionMethod('GuzzleHttp\Ring\Client\CurlFactory', 'createErrorResponse');
        $m->setAccessible(true);
        $response = $m->invoke(
            null,
            function () {},
            [],
            [
                'err_message' => 'foo',
                'curl' => [
                    'errno' => CURLE_COULDNT_CONNECT,
                ]
            ]
        );
        $this->assertInstanceOf('GuzzleHttp\Ring\Exception\ConnectException', $response['error']);
    }

    public function testParsesLastResponseOnly()
    {
        $response1  = [
            'status'  => 301,
            'headers' => [
                'Content-Length' => ['0'],
                'Location' => ['/foo']
            ]
        ];

        $response2 = [
            'status'  => 200,
            'headers' => [
                'Content-Length' => ['0'],
                'Foo' => ['bar']
            ]
        ];

        Server::flush();
        Server::enqueue([$response1, $response2]);

        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'GET',
            'headers'     => ['Host'   => [Server::$host]],
            'client' => [
                'curl' => [
                    CURLOPT_FOLLOWLOCATION => true
                ]
            ]
        ])->wait();

        $this->assertEquals(1, $response['transfer_stats']['redirect_count']);
        $this->assertEquals('http://127.0.0.1:8125/foo', $response['effective_url']);
        $this->assertEquals(['bar'], $response['headers']['Foo']);
        $this->assertEquals(200, $response['status']);
        $this->assertFalse(Core::hasHeader($response, 'Location'));
    }

    public function testMaintainsMultiHeaderOrder()
    {
        Server::flush();
        Server::enqueue([
            [
                'status'  => 200,
                'headers' => [
                    'Content-Length' => ['0'],
                    'Foo' => ['a', 'b'],
                    'foo' => ['c', 'd'],
                ]
            ]
        ]);

        $a = new CurlMultiHandler();
        $response = $a([
            'http_method' => 'GET',
            'headers'     => ['Host'   => [Server::$host]]
        ])->wait();

        $this->assertEquals(
            ['a', 'b', 'c', 'd'],
            Core::headerLines($response, 'Foo')
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Directory /path/to/does/not does not exist for save_to value of /path/to/does/not/exist.txt
     */
    public function testThrowsWhenDirNotFound()
    {
        $request = [
            'http_method' => 'GET',
            'headers' => ['host' => [Server::$url]],
            'client' => ['save_to' => '/path/to/does/not/exist.txt'],
        ];

        $f = new CurlFactory();
        $f($request);
    }
}

}
