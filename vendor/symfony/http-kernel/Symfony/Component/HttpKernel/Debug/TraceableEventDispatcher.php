<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Debug;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface;

/**
 * Collects some data about event listeners.
 *
 * This event dispatcher delegates the dispatching to another one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableEventDispatcher implements EventDispatcherInterface, TraceableEventDispatcherInterface
{
    private $logger;
    private $called = array();
    private $stopwatch;
    private $dispatcher;
    private $wrappedListeners = array();
    private $firstCalledEvent = array();
    private $lastEventId = 0;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
     * @param Stopwatch                $stopwatch  A Stopwatch instance
     * @param LoggerInterface          $logger     A LoggerInterface instance
     */
    public function __construct(EventDispatcherInterface $dispatcher, Stopwatch $stopwatch, LoggerInterface $logger = null)
    {
        $this->dispatcher = $dispatcher;
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
    }

    /**
     * Sets the profiler.
     *
     * The traceable event dispatcher does not use the profiler anymore.
     * The job is now done directly by the Profiler listener and the
     * data collectors themselves.
     *
     * @param Profiler|null $profiler A Profiler instance
     *
     * @deprecated Deprecated since version 2.4, to be removed in 3.0.
     */
    public function setProfiler(Profiler $profiler = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        return $this->dispatcher->removeListener($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        return $this->dispatcher->removeSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        return $this->dispatcher->getListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        return $this->dispatcher->hasListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        $eventId = ++$this->lastEventId;

        $this->preDispatch($eventName, $eventId, $event);

        $e = $this->stopwatch->start($eventName, 'section');

        $this->firstCalledEvent[$eventName] = $this->stopwatch->start($eventName.'.loading', 'event_listener_loading');

        if (!$this->dispatcher->hasListeners($eventName)) {
            $this->firstCalledEvent[$eventName]->stop();
        }

        $this->dispatcher->dispatch($eventName, $event);

        unset($this->firstCalledEvent[$eventName]);

        if ($e->isStarted()) {
            $e->stop();
        }

        $this->postDispatch($eventName, $eventId, $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalledListeners()
    {
        return $this->called;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotCalledListeners()
    {
        $notCalled = array();

        foreach ($this->getListeners() as $name => $listeners) {
            foreach ($listeners as $listener) {
                $info = $this->getListenerInfo($listener, null, $name);
                if (!isset($this->called[$name.'.'.$info['pretty']])) {
                    $notCalled[$name.'.'.$info['pretty']] = $info;
                }
            }
        }

        return $notCalled;
    }

    /**
     * Proxies all method calls to the original event dispatcher.
     *
     * @param string $method    The method name
     * @param array  $arguments The method arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->dispatcher, $method), $arguments);
    }

    /**
     * This is a private method and must not be used.
     *
     * This method is public because it is used in a closure.
     * Whenever Symfony will require PHP 5.4, this could be changed
     * to a proper private method.
     */
    public function logSkippedListeners($eventName, $eventId, Event $event, $listener)
    {
        if (null === $this->logger) {
            return;
        }

        $info = $this->getListenerInfo($listener, $eventId, $eventName);

        $this->logger->debug(sprintf('Listener "%s" stopped propagation of the event "%s".', $info['pretty'], $eventName));

        $skippedListeners = $this->getListeners($eventName);
        $skipped = false;

        foreach ($skippedListeners as $skippedListener) {
            $skippedListener = $this->unwrapListener($skippedListener, $eventId);

            if ($skipped) {
                $info = $this->getListenerInfo($skippedListener, $eventId, $eventName);
                $this->logger->debug(sprintf('Listener "%s" was not called for event "%s".', $info['pretty'], $eventName));
            }

            if ($skippedListener === $listener) {
                $skipped = true;
            }
        }
    }

    /**
     * This is a private method.
     *
     * This method is public because it is used in a closure.
     * Whenever Symfony will require PHP 5.4, this could be changed
     * to a proper private method.
     */
    public function preListenerCall($eventName, $eventId, $listener)
    {
        // is it the first called listener?
        if (isset($this->firstCalledEvent[$eventName])) {
            $this->firstCalledEvent[$eventName]->stop();

            unset($this->firstCalledEvent[$eventName]);
        }

        $info = $this->getListenerInfo($listener, $eventId, $eventName);

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Notified event "%s" to listener "%s".', $eventName, $info['pretty']));
        }

        $this->called[$eventName.'.'.$info['pretty']] = $info;

        return $this->stopwatch->start(isset($info['class']) ? $info['class'] : $info['type'], 'event_listener');
    }

    /**
     * Returns information about the listener
     *
     * @param object   $listener  The listener
     * @param int|null $eventId   The event id
     * @param string   $eventName The event name
     *
     * @return array Information about the listener
     */
    private function getListenerInfo($listener, $eventId, $eventName)
    {
        $listener = $this->unwrapListener($listener, $eventId);

        $info = array(
            'event' => $eventName,
        );
        if ($listener instanceof \Closure) {
            $info += array(
                'type' => 'Closure',
                'pretty' => 'closure',
            );
        } elseif (is_string($listener)) {
            try {
                $r = new \ReflectionFunction($listener);
                $file = $r->getFileName();
                $line = $r->getStartLine();
            } catch (\ReflectionException $e) {
                $file = null;
                $line = null;
            }
            $info += array(
                'type'  => 'Function',
                'function' => $listener,
                'file'  => $file,
                'line'  => $line,
                'pretty' => $listener,
            );
        } elseif (is_array($listener) || (is_object($listener) && is_callable($listener))) {
            if (!is_array($listener)) {
                $listener = array($listener, '__invoke');
            }
            $class = is_object($listener[0]) ? get_class($listener[0]) : $listener[0];
            try {
                $r = new \ReflectionMethod($class, $listener[1]);
                $file = $r->getFileName();
                $line = $r->getStartLine();
            } catch (\ReflectionException $e) {
                $file = null;
                $line = null;
            }
            $info += array(
                'type'  => 'Method',
                'class' => $class,
                'method' => $listener[1],
                'file'  => $file,
                'line'  => $line,
                'pretty' => $class.'::'.$listener[1],
            );
        }

        return $info;
    }

    private function preDispatch($eventName, $eventId, Event $event)
    {
        // wrap all listeners before they are called
        $this->wrappedListeners[$eventId] = new \SplObjectStorage();

        $listeners = $this->dispatcher->getListeners($eventName);

        foreach ($listeners as $listener) {
            $this->dispatcher->removeListener($eventName, $listener);
            $wrapped = $this->wrapListener($eventName, $eventId, $listener);
            $this->wrappedListeners[$eventId][$wrapped] = $listener;
            $this->dispatcher->addListener($eventName, $wrapped);
        }

        switch ($eventName) {
            case KernelEvents::REQUEST:
                $this->stopwatch->openSection();
                break;
            case KernelEvents::VIEW:
            case KernelEvents::RESPONSE:
                // stop only if a controller has been executed
                if ($this->stopwatch->isStarted('controller')) {
                    $this->stopwatch->stop('controller');
                }
                break;
            case KernelEvents::TERMINATE:
                $token = $event->getResponse()->headers->get('X-Debug-Token');
                // There is a very special case when using builtin AppCache class as kernel wrapper, in the case
                // of an ESI request leading to a `stale` response [B]  inside a `fresh` cached response [A].
                // In this case, `$token` contains the [B] debug token, but the  open `stopwatch` section ID
                // is equal to the [A] debug token. Trying to reopen section with the [B] token throws an exception
                // which must be caught.
                try {
                    $this->stopwatch->openSection($token);
                } catch (\LogicException $e) {
                }
                break;
        }
    }

    private function postDispatch($eventName, $eventId, Event $event)
    {
        switch ($eventName) {
            case KernelEvents::CONTROLLER:
                $this->stopwatch->start('controller', 'section');
                break;
            case KernelEvents::RESPONSE:
                $token = $event->getResponse()->headers->get('X-Debug-Token');
                $this->stopwatch->stopSection($token);
                break;
            case KernelEvents::TERMINATE:
                // In the special case described in the `preDispatch` method above, the `$token` section
                // does not exist, then closing it throws an exception which must be caught.
                $token = $event->getResponse()->headers->get('X-Debug-Token');
                try {
                    $this->stopwatch->stopSection($token);
                } catch (\LogicException $e) {
                }
                break;
        }

        foreach ($this->wrappedListeners[$eventId] as $wrapped) {
            $this->dispatcher->removeListener($eventName, $wrapped);
            $this->dispatcher->addListener($eventName, $this->wrappedListeners[$eventId][$wrapped]);
        }

        unset($this->wrappedListeners[$eventId]);
    }

    private function wrapListener($eventName, $eventId, $listener)
    {
        $self = $this;

        return function (Event $event) use ($self, $eventName, $eventId, $listener) {
            $e = $self->preListenerCall($eventName, $eventId, $listener);

            call_user_func($listener, $event, $eventName, $self);

            if ($e->isStarted()) {
                $e->stop();
            }

            if ($event->isPropagationStopped()) {
                $self->logSkippedListeners($eventName, $eventId, $event, $listener);
            }
        };
    }

    private function unwrapListener($listener, $eventId)
    {
        // get the original listener
        if (is_object($listener)) {
            if (null === $eventId) {
                foreach (array_keys($this->wrappedListeners) as $eventId) {
                    if (isset($this->wrappedListeners[$eventId][$listener])) {
                        return $this->wrappedListeners[$eventId][$listener];
                    }
                }
            } elseif (isset($this->wrappedListeners[$eventId][$listener])) {
                return $this->wrappedListeners[$eventId][$listener];
            }
        }

        return $listener;
    }
}
