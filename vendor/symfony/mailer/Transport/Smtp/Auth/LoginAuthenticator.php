<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport\Smtp\Auth;

use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

/**
 * Handles LOGIN authentication.
 *
 * @author Chris Corbyn
 */
class LoginAuthenticator implements AuthenticatorInterface
{
    public function getAuthKeyword(): string
    {
        return 'LOGIN';
    }

    /**
     * @see https://www.ietf.org/rfc/rfc4954.txt
     */
    public function authenticate(EsmtpTransport $client): void
    {
        $client->executeCommand("AUTH LOGIN\r\n", [334]);
        $client->executeCommand(\sprintf("%s\r\n", base64_encode($client->getUsername())), [334]);
        $client->executeCommand(\sprintf("%s\r\n", base64_encode($client->getPassword())), [235]);
    }
}
