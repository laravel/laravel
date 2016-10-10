<?php

namespace React\Promise;

class FunctionMapTest extends TestCase
{
    protected function mapper()
    {
        return function ($val) {
            return $val * 2;
        };
    }

    protected function promiseMapper()
    {
        return function ($val) {
            return resolve($val * 2);
        };
    }

    /** @test */
    public function shouldMapInputValuesArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([2, 4, 6]));

        map(
            [1, 2, 3],
            $this->mapper()
        )->then($mock);
    }

    /** @test */
    public function shouldMapInputPromisesArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([2, 4, 6]));

        map(
            [resolve(1), resolve(2), resolve(3)],
            $this->mapper()
        )->then($mock);
    }

    /** @test */
    public function shouldMapMixedInputArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([2, 4, 6]));

        map(
            [1, resolve(2), 3],
            $this->mapper()
        )->then($mock);
    }

    /** @test */
    public function shouldMapInputWhenMapperReturnsAPromise()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([2, 4, 6]));

        map(
            [1, 2, 3],
            $this->promiseMapper()
        )->then($mock);
    }

    /** @test */
    public function shouldAcceptAPromiseForAnArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([2, 4, 6]));

        map(
            resolve([1, resolve(2), 3]),
            $this->mapper()
        )->then($mock);
    }

    /** @test */
    public function shouldResolveToEmptyArrayWhenInputPromiseDoesNotResolveToArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([]));

        map(
            resolve(1),
            $this->mapper()
        )->then($mock);
    }

    /** @test */
    public function shouldRejectWhenInputContainsRejection()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(2));

        map(
            [resolve(1), reject(2), resolve(3)],
            $this->mapper()
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldRejectWhenInputPromiseRejects()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        map(
            reject(),
            $this->mapper()
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldCancelInputPromise()
    {
        $mock = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock
            ->expects($this->once())
            ->method('cancel');

        map(
            $mock,
            $this->mapper()
        )->cancel();
    }

    /** @test */
    public function shouldCancelInputArrayPromises()
    {
        $mock1 = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock1
            ->expects($this->once())
            ->method('cancel');

        $mock2 = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock2
            ->expects($this->once())
            ->method('cancel');

        map(
            [$mock1, $mock2],
            $this->mapper()
        )->cancel();
    }
}
