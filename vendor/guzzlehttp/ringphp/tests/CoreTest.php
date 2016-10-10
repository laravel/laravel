<?php
namespace GuzzleHttp\Tests\Ring;

use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use GuzzleHttp\Ring\Future\FutureArray;
use GuzzleHttp\Stream\Stream;
use React\Promise\Deferred;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsNullNoHeadersAreSet()
    {
        $this->assertNull(Core::header([], 'Foo'));
        $this->assertNull(Core::firstHeader([], 'Foo'));
    }

    public function testChecksIfHasHeader()
    {
        $message = [
            'headers' => [
                'Foo' => ['Bar', 'Baz'],
                'foo' => ['hello'],
                'bar' => ['1']
            ]
        ];
        $this->assertTrue(Core::hasHeader($message, 'Foo'));
        $this->assertTrue(Core::hasHeader($message, 'foo'));
        $this->assertTrue(Core::hasHeader($message, 'FoO'));
        $this->assertTrue(Core::hasHeader($message, 'bar'));
        $this->assertFalse(Core::hasHeader($message, 'barr'));
    }

    public function testReturnsFirstHeaderWhenSimple()
    {
        $this->assertEquals('Bar', Core::firstHeader([
            'headers' => ['Foo' => ['Bar', 'Baz']],
        ], 'Foo'));
    }

    public function testReturnsFirstHeaderWhenMultiplePerLine()
    {
        $this->assertEquals('Bar', Core::firstHeader([
            'headers' => ['Foo' => ['Bar, Baz']],
        ], 'Foo'));
    }

    public function testExtractsCaseInsensitiveHeader()
    {
        $this->assertEquals(
            'hello',
            Core::header(['headers' => ['foo' => ['hello']]], 'FoO')
        );
    }

    public function testExtractsCaseInsensitiveHeaderLines()
    {
        $this->assertEquals(
            ['a', 'b', 'c', 'd'],
            Core::headerLines([
                'headers' => [
                    'foo' => ['a', 'b'],
                    'Foo' => ['c', 'd']
                ]
            ], 'foo')
        );
    }

    public function testExtractsHeaderLines()
    {
        $this->assertEquals(
            ['bar', 'baz'],
            Core::headerLines([
                'headers' => [
                    'Foo' => ['bar', 'baz'],
                ],
            ], 'Foo')
        );
    }

    public function testExtractsHeaderAsString()
    {
        $this->assertEquals(
            'bar, baz',
            Core::header([
                'headers' => [
                    'Foo' => ['bar', 'baz'],
                ],
            ], 'Foo', true)
        );
    }

    public function testReturnsNullWhenHeaderNotFound()
    {
        $this->assertNull(Core::header(['headers' => []], 'Foo'));
    }

    public function testRemovesHeaders()
    {
        $message = [
            'headers' => [
                'foo' => ['bar'],
                'Foo' => ['bam'],
                'baz' => ['123'],
            ],
        ];

        $this->assertSame($message, Core::removeHeader($message, 'bam'));
        $this->assertEquals([
            'headers' => ['baz' => ['123']],
        ], Core::removeHeader($message, 'foo'));
    }

    public function testCreatesUrl()
    {
        $req = [
            'scheme'  => 'http',
            'headers' => ['host' => ['foo.com']],
            'uri'     => '/',
        ];

        $this->assertEquals('http://foo.com/', Core::url($req));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No Host header was provided
     */
    public function testEnsuresHostIsAvailableWhenCreatingUrls()
    {
        Core::url([]);
    }

    public function testCreatesUrlWithQueryString()
    {
        $req = [
            'scheme'       => 'http',
            'headers'      => ['host' => ['foo.com']],
            'uri'          => '/',
            'query_string' => 'foo=baz',
        ];

        $this->assertEquals('http://foo.com/?foo=baz', Core::url($req));
    }

    public function testUsesUrlIfSet()
    {
        $req = ['url' => 'http://foo.com'];
        $this->assertEquals('http://foo.com', Core::url($req));
    }

    public function testReturnsNullWhenNoBody()
    {
        $this->assertNull(Core::body([]));
    }

    public function testReturnsStreamAsString()
    {
        $this->assertEquals(
            'foo',
            Core::body(['body' => Stream::factory('foo')])
        );
    }

    public function testReturnsString()
    {
        $this->assertEquals('foo', Core::body(['body' => 'foo']));
    }

    public function testReturnsResourceContent()
    {
        $r = fopen('php://memory', 'w+');
        fwrite($r, 'foo');
        rewind($r);
        $this->assertEquals('foo', Core::body(['body' => $r]));
        fclose($r);
    }

    public function testReturnsIteratorContent()
    {
        $a = new \ArrayIterator(['a', 'b', 'cd', '']);
        $this->assertEquals('abcd', Core::body(['body' => $a]));
    }

    public function testReturnsObjectToString()
    {
        $this->assertEquals('foo', Core::body(['body' => new StrClass]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresBodyIsValid()
    {
        Core::body(['body' => false]);
    }

    public function testParsesHeadersFromLines()
    {
        $lines = ['Foo: bar', 'Foo: baz', 'Abc: 123', 'Def: a, b'];
        $this->assertEquals([
            'Foo' => ['bar', 'baz'],
            'Abc' => ['123'],
            'Def' => ['a, b'],
        ], Core::headersFromLines($lines));
    }

    public function testParsesHeadersFromLinesWithMultipleLines()
    {
        $lines = ['Foo: bar', 'Foo: baz', 'Foo: 123'];
        $this->assertEquals([
            'Foo' => ['bar', 'baz', '123'],
        ], Core::headersFromLines($lines));
    }

    public function testCreatesArrayCallFunctions()
    {
        $called = [];
        $a = function ($a, $b) use (&$called) {
            $called['a'] = func_get_args();
        };
        $b = function ($a, $b) use (&$called) {
            $called['b'] = func_get_args();
        };
        $c = Core::callArray([$a, $b]);
        $c(1, 2);
        $this->assertEquals([1, 2], $called['a']);
        $this->assertEquals([1, 2], $called['b']);
    }

    public function testRewindsGuzzleStreams()
    {
        $str = Stream::factory('foo');
        $this->assertTrue(Core::rewindBody(['body' => $str]));
    }

    public function testRewindsStreams()
    {
        $str = Stream::factory('foo')->detach();
        $this->assertTrue(Core::rewindBody(['body' => $str]));
    }

    public function testRewindsIterators()
    {
        $iter = new \ArrayIterator(['foo']);
        $this->assertTrue(Core::rewindBody(['body' => $iter]));
    }

    public function testRewindsStrings()
    {
        $this->assertTrue(Core::rewindBody(['body' => 'hi']));
    }

    public function testRewindsToStrings()
    {
        $this->assertTrue(Core::rewindBody(['body' => new StrClass()]));
    }

    public function typeProvider()
    {
        return [
            ['foo', 'string(3) "foo"'],
            [true, 'bool(true)'],
            [false, 'bool(false)'],
            [10, 'int(10)'],
            [1.0, 'float(1)'],
            [new StrClass(), 'object(GuzzleHttp\Tests\Ring\StrClass)'],
            [['foo'], 'array(1)']
        ];
    }

    /**
     * @dataProvider typeProvider
     */
    public function testDescribesType($input, $output)
    {
        $this->assertEquals($output, Core::describeType($input));
    }

    public function testDoesSleep()
    {
        $t = microtime(true);
        $expected = $t + (100 / 1000);
        Core::doSleep(['client' => ['delay' => 100]]);
        $this->assertGreaterThanOrEqual($expected, microtime(true));
    }

    public function testProxiesFuture()
    {
        $f = new CompletedFutureArray(['status' => 200]);
        $res = null;
        $proxied = Core::proxy($f, function ($value) use (&$res) {
            $value['foo'] = 'bar';
            $res = $value;
            return $value;
        });
        $this->assertNotSame($f, $proxied);
        $this->assertEquals(200, $f->wait()['status']);
        $this->assertArrayNotHasKey('foo', $f->wait());
        $this->assertEquals('bar', $proxied->wait()['foo']);
        $this->assertEquals(200, $proxied->wait()['status']);
    }

    public function testProxiesDeferredFuture()
    {
        $d = new Deferred();
        $f = new FutureArray($d->promise());
        $f2 = Core::proxy($f);
        $d->resolve(['foo' => 'bar']);
        $this->assertEquals('bar', $f['foo']);
        $this->assertEquals('bar', $f2['foo']);
    }

    public function testProxiesDeferredFutureFailure()
    {
        $d = new Deferred();
        $f = new FutureArray($d->promise());
        $f2 = Core::proxy($f);
        $d->reject(new \Exception('foo'));
        try {
            $f2['hello?'];
            $this->fail('did not throw');
        } catch (\Exception $e) {
            $this->assertEquals('foo', $e->getMessage());
        }

    }
}

final class StrClass
{
    public function __toString()
    {
        return 'foo';
    }
}
