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

use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher as BaseTraceableEventDispatcher;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Collects some data about event listeners.
 *
 * This event dispatcher delegates the dispatching to another one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableEventDispatcher extends BaseTraceableEventDispatcher
{
    protected function beforeDispatch(string $eventName, object $event): void
    {
        if ($this->disabled?->__invoke()) {
            return;
        }
        switch ($eventName) {
            case KernelEvents::REQUEST:
                $event->getRequest()->attributes->set('_stopwatch_token', bin2hex(random_bytes(3)));
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
                $sectionId = $event->getRequest()->attributes->get('_stopwatch_token');
                if (null === $sectionId) {
                    break;
                }
                // There is a very special case when using built-in AppCache class as kernel wrapper, in the case
                // of an ESI request leading to a `stale` response [B]  inside a `fresh` cached response [A].
                // In this case, `$token` contains the [B] debug token, but the  open `stopwatch` section ID
                // is equal to the [A] debug token. Trying to reopen section with the [B] token throws an exception
                // which must be caught.
                try {
                    $this->stopwatch->openSection($sectionId);
                } catch (\LogicException) {
                }
                break;
        }
    }

    protected function afterDispatch(string $eventName, object $event): void
    {
        if ($this->disabled?->__invoke()) {
            return;
        }
        switch ($eventName) {
            case KernelEvents::CONTROLLER_ARGUMENTS:
                $this->stopwatch->start('controller', 'section');
                break;
            case KernelEvents::RESPONSE:
                $sectionId = $event->getRequest()->attributes->get('_stopwatch_token');
                if (null === $sectionId) {
                    break;
                }
                try {
                    $this->stopwatch->stopSection($sectionId);
                } catch (\LogicException) {
                    // The stop watch service might have been reset in the meantime
                }
                break;
            case KernelEvents::TERMINATE:
                // In the special case described in the `preDispatch` method above, the `$token` section
                // does not exist, then closing it throws an exception which must be caught.
                $sectionId = $event->getRequest()->attributes->get('_stopwatch_token');
                if (null === $sectionId) {
                    break;
                }
                try {
                    $this->stopwatch->stopSection($sectionId);
                } catch (\LogicException) {
                }
                break;
        }
    }
}
