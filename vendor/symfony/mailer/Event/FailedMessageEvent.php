<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Event;

use Symfony\Component\Mime\RawMessage;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class FailedMessageEvent extends Event
{
    public function __construct(
        private RawMessage $message,
        private \Throwable $error,
    ) {
    }

    public function getMessage(): RawMessage
    {
        return $this->message;
    }

    public function getError(): \Throwable
    {
        return $this->error;
    }
}
