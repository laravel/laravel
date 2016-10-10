<?php

namespace React\Promise;

class FunctionReduceTest extends TestCase
{
    protected function plus()
    {
        return function ($sum, $val) {
            return $sum + $val;
        };
    }

    protected function append()
    {
        return function ($sum, $val) {
            return $sum . $val;
        };
    }

    /** @test */
    public function shouldReduceValuesWithoutInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(6));

        reduce(
            [1, 2, 3],
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldReduceValuesWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        reduce(
            [1, 2, 3],
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReduceValuesWithInitialPromise()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        reduce(
            [1, 2, 3],
            $this->plus(),
            resolve(1)
        )->then($mock);
    }

    /** @test */
    public function shouldReducePromisedValuesWithoutInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(6));

        reduce(
            [resolve(1), resolve(2), resolve(3)],
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldReducePromisedValuesWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        reduce(
            [resolve(1), resolve(2), resolve(3)],
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReducePromisedValuesWithInitialPromise()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        reduce(
            [resolve(1), resolve(2), resolve(3)],
            $this->plus(),
            resolve(1)
        )->then($mock);
    }

    /** @test */
    public function shouldReduceEmptyInputWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        reduce(
            [],
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReduceEmptyInputWithInitialPromise()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        reduce(
            [],
            $this->plus(),
            resolve(1)
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

        reduce(
            [resolve(1), reject(2), resolve(3)],
            $this->plus(),
            resolve(1)
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldResolveWithNullWhenInputIsEmptyAndNoInitialValueOrPromiseProvided()
    {
        // Note: this is different from when.js's behavior!
        // In when.reduce(), this rejects with a TypeError exception (following
        // JavaScript's [].reduce behavior.
        // We're following PHP's array_reduce behavior and resolve with NULL.
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        reduce(
            [],
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldAllowSparseArrayInputWithoutInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(3));

        reduce(
            [null, null, 1, null, 1, 1],
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldAllowSparseArrayInputWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(4));

        reduce(
            [null, null, 1, null, 1, 1],
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReduceInInputOrder()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo('123'));

        reduce(
            [1, 2, 3],
            $this->append(),
            ''
        )->then($mock);
    }

    /** @test */
    public function shouldAcceptAPromiseForAnArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo('123'));

        reduce(
            resolve([1, 2, 3]),
            $this->append(),
            ''
        )->then($mock);
    }

    /** @test */
    public function shouldResolveToInitialValueWhenInputPromiseDoesNotResolveToAnArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        reduce(
            resolve(1),
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldProvideCorrectBasisValue()
    {
        $insertIntoArray = function ($arr, $val, $i) {
            $arr[$i] = $val;

            return $arr;
        };

        $d1 = new Deferred();
        $d2 = new Deferred();
        $d3 = new Deferred();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo([1, 2, 3]));

        reduce(
            [$d1->promise(), $d2->promise(), $d3->promise()],
            $insertIntoArray,
            []
        )->then($mock);

        $d3->resolve(3);
        $d1->resolve(1);
        $d2->resolve(2);
    }

    /** @test */
    public function shouldRejectWhenInputPromiseRejects()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        reduce(
            reject(),
            $this->plus(),
            1
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldCancelInputPromise()
    {
        $mock = $this->getMock('React\Promise\CancellablePromiseInterface');
        $mock
            ->expects($this->once())
            ->method('cancel');

        reduce(
            $mock,
            $this->plus(),
            1
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

        reduce(
            [$mock1, $mock2],
            $this->plus(),
            1
        )->cancel();
    }
}
