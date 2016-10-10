<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Logout;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * This handler clears the passed cookies when a user logs out.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CookieClearingLogoutHandler implements LogoutHandlerInterface
{
    private $cookies;

    /**
     * Constructor.
     *
     * @param array $cookies An array of cookie names to unset
     */
    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * Implementation for the LogoutHandlerInterface. Deletes all requested cookies.
     *
     * @param Request        $request
     * @param Response       $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        foreach ($this->cookies as $cookieName => $cookieData) {
            $response->headers->clearCookie($cookieName, $cookieData['path'], $cookieData['domain']);
        }
    }
}
