<?php

namespace React\Promise\PromiseAdapter;

use React\Promise;

class CallbackPromiseAdapter implements PromiseAdapterInterface
{
    private $callbacks;

    public function __construct(array $callbacks)
    {
        $this->callbacks = $callbacks;
    }

    public function promise()
    {
        return call_user_func_array($this->callbacks['promise'], func_get_args());
    }

    public function resolve()
    {
        return call_user_func_array($this->callbacks['resolve'], func_get_args());
    }

    public function reject()
    {
        return call_user_func_array($this->callbacks['reject'], func_get_args());
    }

    public function notify()
    {
        return call_user_func_array($this->callbacks['notify'], func_get_args());
    }

    public function settle()
    {
        return call_user_func_array($this->callbacks['settle'], func_get_args());
    }
}
