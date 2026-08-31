<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Debug;

use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\VarDumper\Caster\ClassStub;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class WrappedListener
{
    private string|array|object $listener;
    private ?\Closure $optimizedListener;
    private string $name;
    private bool $called = false;
    private bool $stoppedPropagation = false;
    private string $pretty;
    private string $callableRef;
    private ClassStub|string $stub;
    private static bool $hasClassStub;

    public function __construct(
        callable|array $listener,
        ?string $name,
        private Stopwatch $stopwatch,
        private ?EventDispatcherInterface $dispatcher = null,
        private ?int $priority = null,
    ) {
        $this->listener = $listener;
        $this->optimizedListener = $listener instanceof \Closure ? $listener : (\is_callable($listener) ? $listener(...) : null);

        if (\is_array($listener)) {
            [$this->name, $this->callableRef] = $this->parseListener($listener);
            $this->pretty = $this->name.'::'.$listener[1];
            $this->callableRef .= '::'.$listener[1];
        } elseif ($listener instanceof \Closure) {
            $r = new \ReflectionFunction($listener);
            if ($r->isAnonymous()) {
                $this->pretty = $this->name = 'closure';
            } elseif ($class = $r->getClosureCalledClass()) {
                $this->name = $class->name;
                $this->pretty = $this->name.'::'.$r->name;
            } else {
                $this->pretty = $this->name = $r->name;
            }
        } elseif (\is_string($listener)) {
            $this->pretty = $this->name = $listener;
        } else {
            $this->name = get_debug_type($listener);
            $this->pretty = $this->name.'::__invoke';
            $this->callableRef = $listener::class.'::__invoke';
        }

        if (null !== $name) {
            $this->name = $name;
        }

        self::$hasClassStub ??= class_exists(ClassStub::class);
    }

    public function getWrappedListener(): callable|array
    {
        return $this->listener;
    }

    public function wasCalled(): bool
    {
        return $this->called;
    }

    public function stoppedPropagation(): bool
    {
        return $this->stoppedPropagation;
    }

    public function getPretty(): string
    {
        return $this->pretty;
    }

    public function getInfo(string $eventName): array
    {
        $this->stub ??= self::$hasClassStub ? new ClassStub($this->pretty.'()', $this->callableRef ?? $this->listener) : $this->pretty.'()';

        return [
            'event' => $eventName,
            'priority' => $this->priority ??= $this->dispatcher?->getListenerPriority($eventName, $this->listener),
            'pretty' => $this->pretty,
            'stub' => $this->stub,
        ];
    }

    public function __invoke(object $event, string $eventName, EventDispatcherInterface $dispatcher): void
    {
        $dispatcher = $this->dispatcher ?: $dispatcher;

        $this->called = true;
        $this->priority ??= $dispatcher->getListenerPriority($eventName, $this->listener);

        $e = $this->stopwatch->start($this->name, 'event_listener');

        try {
            ($this->optimizedListener ?? $this->listener)($event, $eventName, $dispatcher);
        } finally {
            if ($e->isStarted()) {
                $e->stop();
            }
        }

        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            $this->stoppedPropagation = true;
        }
    }

    private function parseListener(array $listener): array
    {
        if ($listener[0] instanceof \Closure) {
            foreach ((new \ReflectionFunction($listener[0]))->getAttributes(\Closure::class) as $attribute) {
                if ($name = $attribute->getArguments()['name'] ?? false) {
                    return [$name, $attribute->getArguments()['class'] ?? $name];
                }
            }
        }

        if (\is_object($listener[0])) {
            return [get_debug_type($listener[0]), $listener[0]::class];
        }

        return [$listener[0], $listener[0]];
    }
}
