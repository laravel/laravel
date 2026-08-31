<?php

namespace GuzzleHttp;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Creates a composed Guzzle handler function by stacking middlewares on top of
 * an HTTP handler function.
 *
 * @final
 */
class HandlerStack
{
    /**
     * @var (callable(RequestInterface, array): PromiseInterface)|null
     */
    private $handler;

    /**
     * @var array{(callable(callable(RequestInterface, array): PromiseInterface): callable), (string|null)}[]
     */
    private $stack = [];

    /**
     * @var (callable(RequestInterface, array): PromiseInterface)|null
     */
    private $cached;

    /**
     * Creates a default handler stack that can be used by clients.
     *
     * The returned handler will wrap the provided handler or use the most
     * appropriate default handler for your system. The returned HandlerStack has
     * support for cookies, redirects, HTTP error exceptions, and preparing a body
     * before sending.
     *
     * The returned handler stack can be passed to a client in the "handler"
     * option.
     *
     * @param (callable(RequestInterface, array): PromiseInterface)|null $handler HTTP handler function to use with the stack. If no
     *                                                                            handler is provided, the best handler for your
     *                                                                            system will be utilized.
     */
    public static function create(?callable $handler = null): self
    {
        $stack = new self($handler ?: Utils::chooseHandler());
        $stack->push(Middleware::httpErrors(), 'http_errors');
        $stack->push(Middleware::redirect(), 'allow_redirects');
        $stack->push(Middleware::cookies(), 'cookies');
        $stack->push(Middleware::prepareBody(), 'prepare_body');

        return $stack;
    }

    /**
     * @param (callable(RequestInterface, array): PromiseInterface)|null $handler Underlying HTTP handler.
     */
    public function __construct(?callable $handler = null)
    {
        $this->handler = $handler;
    }

    /**
     * Invokes the handler stack as a composed handler
     *
     * @return ResponseInterface|PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $handler = $this->resolve();

        return $handler($request, $options);
    }

    /**
     * Dumps a string representation of the stack.
     *
     * @return string
     */
    public function __toString()
    {
        $depth = 0;
        $stack = [];

        if ($this->handler !== null) {
            $stack[] = '0) Handler: '.$this->debugCallable($this->handler);
        }

        $result = '';
        foreach (\array_reverse($this->stack) as $tuple) {
            ++$depth;
            $str = "{$depth}) Name: '{$tuple[1]}', ";
            $str .= 'Function: '.$this->debugCallable($tuple[0]);
            $result = "> {$str}\n{$result}";
            $stack[] = $str;
        }

        foreach (\array_keys($stack) as $k) {
            $result .= "< {$stack[$k]}\n";
        }

        return $result;
    }

    /**
     * Set the HTTP handler that actually returns a promise.
     *
     * @param callable(RequestInterface, array): PromiseInterface $handler Accepts a request and array of options and
     *                                                                     returns a Promise.
     */
    public function setHandler(callable $handler): void
    {
        $this->handler = $handler;
        $this->cached = null;
    }

    /**
     * Returns true if the builder has a handler.
     */
    public function hasHandler(): bool
    {
        return $this->handler !== null;
    }

    /**
     * Unshift a middleware to the bottom of the stack.
     *
     * @param callable(callable): callable $middleware Middleware function
     * @param string                       $name       Name to register for this middleware.
     */
    public function unshift(callable $middleware, ?string $name = null): void
    {
        \array_unshift($this->stack, [$middleware, $name]);
        $this->cached = null;
    }

    /**
     * Push a middleware to the top of the stack.
     *
     * @param callable(callable): callable $middleware Middleware function
     * @param string                       $name       Name to register for this middleware.
     */
    public function push(callable $middleware, string $name = ''): void
    {
        $this->stack[] = [$middleware, $name];
        $this->cached = null;
    }

    /**
     * Add a middleware before another middleware by name.
     *
     * @param string                       $findName   Middleware to find
     * @param callable(callable): callable $middleware Middleware function
     * @param string                       $withName   Name to register for this middleware.
     */
    public function before(string $findName, callable $middleware, string $withName = ''): void
    {
        $this->splice($findName, $withName, $middleware, true);
    }

    /**
     * Add a middleware after another middleware by name.
     *
     * @param string                       $findName   Middleware to find
     * @param callable(callable): callable $middleware Middleware function
     * @param string                       $withName   Name to register for this middleware.
     */
    public function after(string $findName, callable $middleware, string $withName = ''): void
    {
        $this->splice($findName, $withName, $middleware, false);
    }

    /**
     * Remove a middleware by instance or name from the stack.
     *
     * @param callable|string $remove Middleware to remove by instance or name.
     */
    public function remove($remove): void
    {
        if (!is_string($remove) && !is_callable($remove)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a callable or string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->cached = null;

        if (\is_string($remove)) {
            $count = \count($this->stack);
            $this->stack = \array_values(\array_filter(
                $this->stack,
                static function ($tuple) use ($remove) {
                    return $tuple[1] !== $remove;
                }
            ));

            if ($count !== \count($this->stack) || !\is_callable($remove)) {
                return;
            }
        }

        $this->stack = \array_values(\array_filter(
            $this->stack,
            static function ($tuple) use ($remove) {
                return $tuple[0] !== $remove;
            }
        ));
    }

    /**
     * Compose the middleware and handler into a single callable function.
     *
     * @return callable(RequestInterface, array): PromiseInterface
     */
    public function resolve(): callable
    {
        if ($this->cached === null) {
            if (($prev = $this->handler) === null) {
                throw new \LogicException('No handler has been specified');
            }

            foreach (\array_reverse($this->stack) as $fn) {
                /** @var callable(RequestInterface, array): PromiseInterface $prev */
                $prev = $fn[0]($prev);
            }

            $this->cached = $prev;
        }

        return $this->cached;
    }

    private function findByName(string $name): int
    {
        foreach ($this->stack as $k => $v) {
            if ($v[1] === $name) {
                return $k;
            }
        }

        throw new \InvalidArgumentException("Middleware not found: $name");
    }

    /**
     * Splices a function into the middleware list at a specific position.
     */
    private function splice(string $findName, string $withName, callable $middleware, bool $before): void
    {
        $this->cached = null;
        $idx = $this->findByName($findName);
        $tuple = [$middleware, $withName];

        if ($before) {
            if ($idx === 0) {
                \array_unshift($this->stack, $tuple);
            } else {
                $replacement = [$tuple, $this->stack[$idx]];
                \array_splice($this->stack, $idx, 1, $replacement);
            }
        } elseif ($idx === \count($this->stack) - 1) {
            $this->stack[] = $tuple;
        } else {
            $replacement = [$this->stack[$idx], $tuple];
            \array_splice($this->stack, $idx, 1, $replacement);
        }
    }

    /**
     * Provides a debug string for a given callable.
     *
     * @param callable|string $fn Function to write as a string.
     */
    private function debugCallable($fn): string
    {
        if (\is_string($fn)) {
            return "callable({$fn})";
        }

        if (\is_array($fn)) {
            return \is_string($fn[0])
                ? "callable({$fn[0]}::{$fn[1]})"
                : "callable(['".\get_class($fn[0])."', '{$fn[1]}'])";
        }

        /** @var object $fn */
        return 'callable('.\spl_object_hash($fn).')';
    }
}
