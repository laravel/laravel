<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base class for events dispatched in the HttpKernel component.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class KernelEvent extends Event
{
    /**
     * @param int $requestType The request type the kernel is currently processing; one of
     *                         HttpKernelInterface::MAIN_REQUEST or HttpKernelInterface::SUB_REQUEST
     */
    public function __construct(
        private HttpKernelInterface $kernel,
        private Request $request,
        private ?int $requestType,
    ) {
    }

    /**
     * Returns the kernel in which this event was thrown.
     */
    public function getKernel(): HttpKernelInterface
    {
        return $this->kernel;
    }

    /**
     * Returns the request the kernel is currently processing.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns the request type the kernel is currently processing.
     *
     * @return int One of HttpKernelInterface::MAIN_REQUEST and
     *             HttpKernelInterface::SUB_REQUEST
     */
    public function getRequestType(): int
    {
        return $this->requestType;
    }

    /**
     * Checks if this is the main request.
     */
    public function isMainRequest(): bool
    {
        return HttpKernelInterface::MAIN_REQUEST === $this->requestType;
    }
}
