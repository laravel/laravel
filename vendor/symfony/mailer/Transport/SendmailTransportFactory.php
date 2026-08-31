<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport;

use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
final class SendmailTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        if ('sendmail+smtp' === $dsn->getScheme() || 'sendmail' === $dsn->getScheme()) {
            return new SendmailTransport($dsn->getOption('command'), $this->dispatcher, $this->logger);
        }

        throw new UnsupportedSchemeException($dsn, 'sendmail', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return ['sendmail', 'sendmail+smtp'];
    }
}
