<?php

namespace Stack;

use Symfony\Component\HttpKernel\HttpKernelInterface;

class Builder
{
    private $specs;

    public function __construct()
    {
        $this->specs = new \SplStack();
    }

    public function unshift(/*$kernelClass, $args...*/)
    {
        if (func_num_args() === 0) {
            throw new \InvalidArgumentException("Missing argument(s) when calling unshift");
        }

        $spec = func_get_args();
        $this->specs->unshift($spec);

        return $this;
    }

    public function push(/*$kernelClass, $args...*/)
    {
        if (func_num_args() === 0) {
            throw new \InvalidArgumentException("Missing argument(s) when calling push");
        }

        $spec = func_get_args();
        $this->specs->push($spec);

        return $this;
    }

    public function resolve(HttpKernelInterface $app)
    {
        $middlewares = array($app);

        foreach ($this->specs as $spec) {
            $args = $spec;
            $firstArg = array_shift($args);

            if (is_callable($firstArg)) {
                $app = $firstArg($app);
            } else {
                $kernelClass = $firstArg;
                array_unshift($args, $app);

                $reflection = new \ReflectionClass($kernelClass);
                $app = $reflection->newInstanceArgs($args);
            }

            array_unshift($middlewares, $app);
        }

        return new StackedHttpKernel($app, $middlewares);
    }
}
