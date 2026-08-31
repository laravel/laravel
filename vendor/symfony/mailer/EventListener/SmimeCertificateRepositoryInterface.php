<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\EventListener;

/**
 * Encrypts messages using S/MIME.
 *
 * @author Florent Morselli <florent.morselli@spomky-labs.com>
 */
interface SmimeCertificateRepositoryInterface
{
    /**
     * @return ?string The path to the certificate. null if not found
     */
    public function findCertificatePathFor(string $email): ?string;
}
