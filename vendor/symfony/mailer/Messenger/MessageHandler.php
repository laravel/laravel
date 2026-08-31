<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Messenger;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MessageHandler
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    public function __invoke(SendEmailMessage $message): ?SentMessage
    {
        return $this->transport->send($message->getMessage(), $message->getEnvelope());
    }
}
