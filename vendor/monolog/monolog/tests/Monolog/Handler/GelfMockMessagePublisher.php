<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Gelf\MessagePublisher;
use Gelf\Message;

class GelfMockMessagePublisher extends MessagePublisher
{
    public function publish(Message $message)
    {
        $this->lastMessage = $message;
    }

    public $lastMessage = null;
}
