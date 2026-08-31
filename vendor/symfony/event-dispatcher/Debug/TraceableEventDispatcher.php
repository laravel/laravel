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
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Collects some data about event listeners.
 *
 * This event dispatcher delegates the dispatching to another one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableEventDispatcher implements EventDispatcherInterface, ResetInterface
{
    /**
     * @var \SplObjectStorage<WrappedListener, array{string, string}>|null
     */
    private ?\SplObjectStorage $callStack = null;
    private array $wrappedListeners = [];
    private array $orphanedEvents = [];
    private array $dispatchDepth = [];
    private array $calledListenerInfos = [];
    private array $calledOriginalListeners = [];
    private string $currentRequestHash = '';

    public function __construct(
        private EventDispatcherInterface $dispatcher,
        protected Stopwatch $stopwatch,
        protected ?LoggerInterface $logger = null,
        private ?RequestStack $requestStack = null,
        protected readonly ?\Closure $disabled = null,
    ) {
    }

    public function addListener(string $eventName, callable|array $listener, int $priority = 0): void
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    public function removeListener(string $eventName, callable|array $listener): void
    {
        if (isset($this->wrappedListeners[$eventName])) {
            foreach ($this->wrappedListeners[$eventName] as $index => $wrappedListener) {
                if ($wrappedListener->getWrappedListener() === $listener || ($listener instanceof \Closure && $wrappedListener->getWrappedListener() == $listener)) {
                    $listener = $wrappedListener;
                    unset($this->wrappedListeners[$eventName][$index]);
                    break;
                }
            }
        }

        $this->dispatcher->removeListener($eventName, $listener);
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        $this->dispatcher->removeSubscriber($subscriber);
    }

    public function getListeners(?string $eventName = null): array
    {
        return $this->dispatcher->getListeners($eventName);
    }

    public function getListenerPriority(string $eventName, callable|array $listener): ?int
    {
        // we might have wrapped listeners for the event (if called while dispatching)
        // in that case get the priority by wrapper
        if (isset($this->wrappedListeners[$eventName])) {
            foreach ($this->wrappedListeners[$eventName] as $wrappedListener) {
                if ($wrappedListener->getWrappedListener() === $listener || ($listener instanceof \Closure && $wrappedListener->getWrappedListener() == $listener)) {
                    return $this->dispatcher->getListenerPriority($eventName, $wrappedListener);
                }
            }
        }

        return $this->dispatcher->getListenerPriority($eventName, $listener);
    }

    public function hasListeners(?string $eventName = null): bool
    {
        return $this->dispatcher->hasListeners($eventName);
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        if ($this->disabled?->__invoke()) {
            return $this->dispatcher->dispatch($event, $eventName);
        }
        $eventName ??= $event::class;

        $this->callStack ??= new \SplObjectStorage();

        $currentRequestHash = $this->currentRequestHash = $this->requestStack && ($request = $this->requestStack->getCurrentRequest()) ? spl_object_hash($request) : '';

        if (null !== $this->logger && $event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            $this->logger->debug(\sprintf('The "%s" event is already stopped. No listeners have been called.', $eventName));
        }

        $this->preProcess($eventName);
        try {
            $this->beforeDispatch($eventName, $event);
            try {
                $e = $this->stopwatch->start($eventName, 'section');
                try {
                    $this->dispatcher->dispatch($event, $eventName);
                } finally {
                    if ($e->isStarted()) {
                        $e->stop();
                    }
                }
            } finally {
                $this->afterDispatch($eventName, $event);
            }
        } finally {
            $this->currentRequestHash = $currentRequestHash;
            $this->postProcess($eventName);
        }

        return $event;
    }

    public function getCalledListeners(?Request $request = null): array
    {
        if (!$this->calledListenerInfos) {
            return [];
        }

        $hash = $request ? spl_object_hash($request) : null;
        $called = [];

        foreach ($this->calledListenerInfos as $requestHash => $infos) {
            if (null === $hash || $hash === $requestHash) {
                $called[] = $infos;
            }
        }

        return $called ? array_merge(...$called) : [];
    }

    public function getNotCalledListeners(?Request $request = null): array
    {
        try {
            $allListeners = $this->dispatcher instanceof EventDispatcher ? $this->getListenersWithPriority() : $this->getListenersWithoutPriority();
        } catch (\Exception $e) {
            $this->logger?->info('An exception was thrown while getting the uncalled listeners.', ['exception' => $e]);

            // unable to retrieve the uncalled listeners
            return [];
        }

        $hash = $request ? spl_object_hash($request) : null;
        $calledListeners = [];

        foreach ($this->calledOriginalListeners as $requestHash => $eventListeners) {
            if (null === $hash || $hash === $requestHash) {
                $calledListeners[] = array_merge(...array_values($eventListeners));
            }
        }

        $calledListeners = $calledListeners ? array_merge(...$calledListeners) : [];

        $notCalled = [];

        foreach ($allListeners as $eventName => $listeners) {
            foreach ($listeners as [$listener, $priority]) {
                if (!\in_array($listener, $calledListeners, true)) {
                    if (!$listener instanceof WrappedListener) {
                        $listener = new WrappedListener($listener, null, $this->stopwatch, $this, $priority);
                    }
                    $notCalled[] = $listener->getInfo($eventName);
                }
            }
        }

        uasort($notCalled, $this->sortNotCalledListeners(...));

        return $notCalled;
    }

    public function getOrphanedEvents(?Request $request = null): array
    {
        if ($request) {
            return $this->orphanedEvents[spl_object_hash($request)] ?? [];
        }

        if (!$this->orphanedEvents) {
            return [];
        }

        return array_merge(...array_values($this->orphanedEvents));
    }

    public function reset(): void
    {
        $this->callStack = null;
        $this->orphanedEvents = [];
        $this->currentRequestHash = '';
        $this->dispatchDepth = [];
        $this->calledListenerInfos = [];
        $this->calledOriginalListeners = [];
    }

    /**
     * Proxies all method calls to the original event dispatcher.
     *
     * @param string $method    The method name
     * @param array  $arguments The method arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        return $this->dispatcher->{$method}(...$arguments);
    }

    /**
     * Called before dispatching the event.
     */
    protected function beforeDispatch(string $eventName, object $event): void
    {
    }

    /**
     * Called after dispatching the event.
     */
    protected function afterDispatch(string $eventName, object $event): void
    {
    }

    private function preProcess(string $eventName): void
    {
        $this->dispatchDepth[$eventName] = ($this->dispatchDepth[$eventName] ?? 0) + 1;

        if (!$this->dispatcher->hasListeners($eventName)) {
            $this->orphanedEvents[$this->currentRequestHash][] = $eventName;

            return;
        }

        foreach ($this->dispatcher->getListeners($eventName) as $listener) {
            $priority = $this->getListenerPriority($eventName, $listener) ?? 0;
            $wrappedListener = new WrappedListener($listener instanceof WrappedListener ? $listener->getWrappedListener() : $listener, null, $this->stopwatch, $this);
            $this->wrappedListeners[$eventName][] = $wrappedListener;
            $this->dispatcher->removeListener($eventName, $listener);
            $this->dispatcher->addListener($eventName, $wrappedListener, $priority);
            $this->callStack[$wrappedListener] = [$eventName, $this->currentRequestHash];
        }
    }

    private function postProcess(string $eventName): void
    {
        if (null === $this->callStack) {
            return;
        }

        $this->dispatchDepth[$eventName] = ($this->dispatchDepth[$eventName] ?? 1) - 1;

        unset($this->wrappedListeners[$eventName]);
        $skipped = false;
        foreach ($this->dispatcher->getListeners($eventName) as $listener) {
            if (!$listener instanceof WrappedListener) { // #12845: a new listener was added during dispatch.
                continue;
            }
            // Unwrap listener
            $priority = $this->getListenerPriority($eventName, $listener);
            $this->dispatcher->removeListener($eventName, $listener);
            $this->dispatcher->addListener($eventName, $listener->getWrappedListener(), $priority);

            if (null !== $this->logger) {
                $context = ['event' => $eventName, 'listener' => $listener->getPretty()];
            }

            if ($listener->wasCalled()) {
                $this->logger?->debug('Notified event "{event}" to listener "{listener}".', $context);

                $original = $listener->getWrappedListener();
                if (!\in_array($original, $this->calledOriginalListeners[$this->currentRequestHash][$eventName] ?? [], true)) {
                    $this->calledOriginalListeners[$this->currentRequestHash][$eventName][] = $original;
                    $this->calledListenerInfos[$this->currentRequestHash][] = $listener->getInfo($eventName);
                }
            }

            unset($this->callStack[$listener]);

            if (null !== $this->logger && $skipped) {
                $this->logger->debug('Listener "{listener}" was not called for event "{event}".', $context);
            }

            if ($listener->stoppedPropagation()) {
                $this->logger?->debug('Listener "{listener}" stopped propagation of the event "{event}".', $context);

                $skipped = true;
            }
        }

        if (0 < $this->dispatchDepth[$eventName]) {
            return;
        }

        // Clean up stale callStack entries left by nested same-event dispatches
        $stale = [];
        foreach ($this->callStack as $listener) {
            if ($this->callStack->getInfo()[0] === $eventName) {
                $stale[] = $listener;
            }
        }
        foreach ($stale as $listener) {
            if ($listener->wasCalled()) {
                $original = $listener->getWrappedListener();
                if (!\in_array($original, $this->calledOriginalListeners[$this->currentRequestHash][$eventName] ?? [], true)) {
                    $this->calledOriginalListeners[$this->currentRequestHash][$eventName][] = $original;
                    $this->calledListenerInfos[$this->currentRequestHash][] = $listener->getInfo($eventName);
                }
            }
            unset($this->callStack[$listener]);
        }
    }

    private function sortNotCalledListeners(array $a, array $b): int
    {
        if (0 !== $cmp = strcmp($a['event'], $b['event'])) {
            return $cmp;
        }

        if (\is_int($a['priority']) && !\is_int($b['priority'])) {
            return 1;
        }

        if (!\is_int($a['priority']) && \is_int($b['priority'])) {
            return -1;
        }

        if ($a['priority'] === $b['priority']) {
            return 0;
        }

        if ($a['priority'] > $b['priority']) {
            return -1;
        }

        return 1;
    }

    private function getListenersWithPriority(): array
    {
        $result = [];

        $allListeners = new \ReflectionProperty(EventDispatcher::class, 'listeners');

        foreach ($allListeners->getValue($this->dispatcher) as $eventName => $listenersByPriority) {
            foreach ($listenersByPriority as $priority => $listeners) {
                foreach ($listeners as $listener) {
                    $result[$eventName][] = [$listener, $priority];
                }
            }
        }

        return $result;
    }

    private function getListenersWithoutPriority(): array
    {
        $result = [];

        foreach ($this->getListeners() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $result[$eventName][] = [$listener, null];
            }
        }

        return $result;
    }
}
