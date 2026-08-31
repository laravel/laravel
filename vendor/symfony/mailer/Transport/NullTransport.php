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

use Symfony\Component\Mailer\SentMessage;

/**
 * Pretends messages have been sent, but just ignores them.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class NullTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
    }

    public function __toString(): string
    {
        return 'null://';
    }
}
