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

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Allows to create a response for a thrown exception
 *
 * Call setResponse() to set the response that will be returned for the
 * current request. The propagation of this event is stopped as soon as a
 * response is set.
 *
 * You can also call setException() to replace the thrown exception. This
 * exception will be thrown if no response is set during processing of this
 * event.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class GetResponseForExceptionEvent extends GetResponseEvent
{
    /**
     * The exception object
     * @var \Exception
     */
    private $exception;

    public function __construct(HttpKernelInterface $kernel, Request $request, $requestType, \Exception $e)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->setException($e);
    }

    /**
     * Returns the thrown exception
     *
     * @return \Exception  The thrown exception
     *
     * @api
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Replaces the thrown exception
     *
     * This exception will be thrown if no response is set in the event.
     *
     * @param \Exception $exception The thrown exception
     *
     * @api
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
