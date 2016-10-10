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

use Symfony\Component\HttpFoundation\Response;

/**
 * Allows to create a response for a request
 *
 * Call setResponse() to set the response that will be returned for the
 * current request. The propagation of this event is stopped as soon as a
 * response is set.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class GetResponseEvent extends KernelEvent
{
    /**
     * The response object
     * @var Response
     */
    private $response;

    /**
     * Returns the response object
     *
     * @return Response
     *
     * @api
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation
     *
     * @param Response $response
     *
     * @api
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    /**
     * Returns whether a response was set
     *
     * @return bool    Whether a response was set
     *
     * @api
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}
