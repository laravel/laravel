<?php

namespace React\Promise;

class CancellationQueueTest extends TestCase
{
    /** @test */
    public function acceptsSimpleCancellableThenable()
    {
        $p = new SimpleTestCancellableThenable();

        $cancellationQueue = new CancellationQueue();
        $cancellationQueue->enqueue($p);

        $cancellationQueue();

        $this->assertTrue($p->cancelCalled);
    }

    /** @test */
    public function ignoresSimpleCancellable()
    {
        $p = new SimpleTestCancellable();

        $cancellationQueue = new CancellationQueue();
        $cancellationQueue->enqueue($p);

        $cancellationQueue();

        $this->assertFalse($p->cancelCalled);
    }

    /** @test */
    public function callsCancelOnPromisesEnqueuedBeforeStart()
    {
        $d1 = $this->getCancellableDeferred();
        $d2 = $this->getCancellableDeferred();

        $cancellationQueue = new CancellationQueue();
        $cancellationQueue->enqueue($d1->promise());
        $cancellationQueue->enqueue($d2->promise());

        $cancellationQueue();
    }

    /** @test */
    public function callsCancelOnPromisesEnqueuedAfterStart()
    {
        $d1 = $this->getCancellableDeferred();
        $d2 = $this->getCancellableDeferred();

        $cancellationQueue = new CancellationQueue();

        $cancellationQueue();

        $cancellationQueue->enqueue($d2->promise());
        $cancellationQueue->enqueue($d1->promise());
    }

    /** @test */
    public function doesNotCallCancelTwiceWhenStartedTwice()
    {
        $d = $this->getCancellableDeferred();

        $cancellationQueue = new CancellationQueue();
        $cancellationQueue->enqueue($d->promise());

        $cancellationQueue();
        $cancellationQueue();
    }

    /** @test */
    public function rethrowsExceptionsThrownFromCancel()
    {
        $this->setExpectedException('\Exception', 'test');

        $mock = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock
            ->expects($this->once())
            ->method('cancel')
            ->will($this->throwException(new \Exception('test')));

        $cancellationQueue = new CancellationQueue();
        $cancellationQueue->enqueue($mock);

        $cancellationQueue();
    }

    private function getCancellableDeferred()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke');

        return new Deferred($mock);
    }
}
