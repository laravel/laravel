<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * ProfilerListener collects data for the current request by listening to the kernel events.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ProfilerListener implements EventSubscriberInterface
{
    private ?\Throwable $exception = null;
    /** @var \SplObjectStorage<Request, Profile> */
    private \SplObjectStorage $profiles;
    /** @var \SplObjectStorage<Request, Request|null> */
    private \SplObjectStorage $parents;

    /**
     * @param bool $onlyException    True if the profiler only collects data when an exception occurs, false otherwise
     * @param bool $onlyMainRequests True if the profiler only collects data when the request is the main request, false otherwise
     */
    public function __construct(
        private Profiler $profiler,
        private RequestStack $requestStack,
        private ?RequestMatcherInterface $matcher = null,
        private bool $onlyException = false,
        private bool $onlyMainRequests = false,
        private ?string $collectParameter = null,
    ) {
        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
    }

    /**
     * Handles the onKernelException event.
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->onlyMainRequests && !$event->isMainRequest()) {
            return;
        }

        $this->exception = $event->getThrowable();
    }

    /**
     * Handles the onKernelResponse event.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->onlyMainRequests && !$event->isMainRequest()) {
            return;
        }

        if ($this->onlyException && null === $this->exception) {
            return;
        }

        $request = $event->getRequest();
        if (null !== $this->collectParameter && null !== $collectParameterValue = $request->attributes->get($this->collectParameter) ?? $request->query->get($this->collectParameter) ?? $request->request->get($this->collectParameter)) {
            filter_var($collectParameterValue, \FILTER_VALIDATE_BOOL) ? $this->profiler->enable() : $this->profiler->disable();
        }

        $exception = $this->exception;
        $this->exception = null;

        if (null !== $this->matcher && !$this->matcher->matches($request)) {
            return;
        }

        $session = !$request->attributes->getBoolean('_stateless') && $request->hasPreviousSession() ? $request->getSession() : null;

        if ($session instanceof Session) {
            $usageIndexValue = $usageIndexReference = &$session->getUsageIndex();
            $usageIndexReference = \PHP_INT_MIN;
        }

        try {
            if (!$profile = $this->profiler->collect($request, $event->getResponse(), $exception)) {
                return;
            }
        } finally {
            if ($session instanceof Session) {
                $usageIndexReference = $usageIndexValue;
            }
        }

        $this->profiles[$request] = $profile;

        $this->parents[$request] = $this->requestStack->getParentRequest();
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        // attach children to parents
        foreach ($this->profiles as $request) {
            if (null !== $parentRequest = $this->parents[$request]) {
                if (isset($this->profiles[$parentRequest])) {
                    $this->profiles[$parentRequest]->addChild($this->profiles[$request]);
                }
            }
        }

        // save profiles
        foreach ($this->profiles as $request) {
            $this->profiler->saveProfile($this->profiles[$request]);
        }

        $this->reset();
    }

    public function reset(): void
    {
        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
        $this->exception = null;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -100],
            KernelEvents::EXCEPTION => ['onKernelException', 0],
            KernelEvents::TERMINATE => ['onKernelTerminate', -1024],
        ];
    }
}
