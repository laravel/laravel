<?php

namespace React\Promise\PromiseTest;

trait FullTestTrait
{
    use PromisePendingTestTrait,
        PromiseSettledTestTrait,
        PromiseFulfilledTestTrait,
        PromiseRejectedTestTrait,
        ResolveTestTrait,
        RejectTestTrait,
        NotifyTestTrait,
        CancelTestTrait;
}
