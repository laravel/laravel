<?php
namespace GuzzleHttp\Tests\Ring\Future;

use GuzzleHttp\Ring\Exception\CancelledFutureAccessException;
use GuzzleHttp\Ring\Future\FutureValue;
use React\Promise\Deferred;

class FutureValueTest extends \PHPUnit_Framework_TestCase
{
    public function testDerefReturnsValue()
    {
        $called = 0;
        $deferred = new Deferred();

        $f = new FutureValue(
            $deferred->promise(),
            function () use ($deferred, &$called) {
                $called++;
                $deferred->resolve('foo');
            }
        );

        $this->assertEquals('foo', $f->wait());
        $this->assertEquals(1, $called);
        $this->assertEquals('foo', $f->wait());
        $this->assertEquals(1, $called);
        $f->cancel();
        $this->assertTrue($this->readAttribute($f, 'isRealized'));
    }

    /**
     * @expectedException \GuzzleHttp\Ring\Exception\CancelledFutureAccessException
     */
    public function testThrowsWhenAccessingCancelled()
    {
        $f = new FutureValue(
            (new Deferred())->promise(),
            function () {},
            function () { return true; }
        );
        $f->cancel();
        $f->wait();
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testThrowsWhenDerefFailure()
    {
        $called = false;
        $deferred = new Deferred();
        $f = new FutureValue(
            $deferred->promise(),
            function () use(&$called) {
                $called = true;
            }
        );
        $deferred->reject(new \OutOfBoundsException());
        $f->wait();
        $this->assertFalse($called);
    }

    /**
     * @expectedException \GuzzleHttp\Ring\Exception\RingException
     * @expectedExceptionMessage Waiting did not resolve future
     */
    public function testThrowsWhenDerefDoesNotResolve()
    {
        $deferred = new Deferred();
        $f = new FutureValue(
            $deferred->promise(),
            function () use(&$called) {
                $called = true;
            }
        );
        $f->wait();
    }

    public function testThrowingCancelledFutureAccessExceptionCancels()
    {
        $deferred = new Deferred();
        $f = new FutureValue(
            $deferred->promise(),
            function () use ($deferred) {
                throw new CancelledFutureAccessException();
            }
        );
        try {
            $f->wait();
            $this->fail('did not throw');
        } catch (CancelledFutureAccessException $e) {}
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage foo
     */
    public function testThrowingExceptionInDerefMarksAsFailed()
    {
        $deferred = new Deferred();
        $f = new FutureValue(
            $deferred->promise(),
            function () {
                throw new \Exception('foo');
            }
        );
        $f->wait();
    }
}
