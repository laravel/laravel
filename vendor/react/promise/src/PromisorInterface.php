<?php

namespace React\Promise;

interface PromisorInterface
{
    /**
     * @return PromiseInterface
     */
    public function promise();
}
