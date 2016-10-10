<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core;

final class AuthenticationEvents
{
    /**
     * The AUTHENTICATION_SUCCESS event occurs after a user is authenticated
     * by one provider.
     *
     * The event listener method receives a
     * Symfony\Component\Security\Core\Event\AuthenticationEvent instance.
     *
     * @var string
     */
    const AUTHENTICATION_SUCCESS = 'security.authentication.success';

    /**
     * The AUTHENTICATION_FAILURE event occurs after a user cannot be
     * authenticated by any of the providers.
     *
     * The event listener method receives a
     * Symfony\Component\Security\Core\Event\AuthenticationFailureEvent
     * instance.
     *
     * @var string
     */
    const AUTHENTICATION_FAILURE = 'security.authentication.failure';
}
