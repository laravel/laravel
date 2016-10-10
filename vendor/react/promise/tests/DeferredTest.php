<?php

namespace React\Promise;

use React\Promise\PromiseAdapter\CallbackPromiseAdapter;

class DeferredTest extends TestCase
{
    use PromiseTest\FullTestTrait;

    public function getPromiseTestAdapter(callable $canceller = null)
    {
        $d = new Deferred($canceller);

        return new CallbackPromiseAdapter([
            'promise' => [$d, 'promise'],
            'resolve' => [$d, 'resolve'],
            'reject'  => [$d, 'reject'],
            'notify'  => [$d, 'progress'],
            'settle'  => [$d, 'resolve'],
        ]);
    }

    /** @test */
    public function progressIsAnAliasForNotify()
    {
        $deferred = new Deferred();

        $sentinel = new \stdClass();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($sentinel);

        $deferred->promise()
            ->then($this->expectCallableNever(), $this->expectCallableNever(), $mock);

        $deferred->progress($sentinel);
    }
}
