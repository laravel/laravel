<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * InteractiveLoginEvent
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class InteractiveLoginEvent extends Event
{
    private $request;
    private $authenticationToken;

    /**
     * Constructor.
     *
     * @param Request        $request             A Request instance
     * @param TokenInterface $authenticationToken A TokenInterface instance
     */
    public function __construct(Request $request, TokenInterface $authenticationToken)
    {
        $this->request = $request;
        $this->authenticationToken = $authenticationToken;
    }

    /**
     * Gets the request.
     *
     * @return Request A Request instance
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets the authentication token.
     *
     * @return TokenInterface A TokenInterface instance
     */
    public function getAuthenticationToken()
    {
        return $this->authenticationToken;
    }
}
