<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Provider;

use Symfony\Component\Translation\Exception\IncompleteDsnException;

abstract class AbstractProviderFactory implements ProviderFactoryInterface
{
    public function supports(Dsn $dsn): bool
    {
        return \in_array($dsn->getScheme(), $this->getSupportedSchemes(), true);
    }

    /**
     * @return string[]
     */
    abstract protected function getSupportedSchemes(): array;

    protected function getUser(Dsn $dsn): string
    {
        return $dsn->getUser() ?? throw new IncompleteDsnException('User is not set.', $dsn->getScheme().'://'.$dsn->getHost());
    }

    protected function getPassword(Dsn $dsn): string
    {
        return $dsn->getPassword() ?? throw new IncompleteDsnException('Password is not set.', $dsn->getOriginalDsn());
    }
}
