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
final class NullTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        if ('null' === $dsn->getScheme()) {
            return new NullTransport($this->dispatcher, $this->logger);
        }

        throw new UnsupportedSchemeException($dsn, 'null', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return ['null'];
    }
}
