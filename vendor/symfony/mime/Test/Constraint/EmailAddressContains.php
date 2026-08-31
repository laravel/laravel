<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\Mime\Header\MailboxHeader;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\RawMessage;

final class EmailAddressContains extends Constraint
{
    public function __construct(
        private string $headerName,
        private string $expectedValue,
    ) {
    }

    public function toString(): string
    {
        return \sprintf('contains address "%s" with value "%s"', $this->headerName, $this->expectedValue);
    }

    /**
     * @param RawMessage $message
     */
    protected function matches($message): bool
    {
        if (RawMessage::class === $message::class) {
            throw new \LogicException('Unable to test a message address on a RawMessage instance.');
        }

        $header = $message->getHeaders()->get($this->headerName);
        if ($header instanceof MailboxHeader) {
            return $this->expectedValue === $header->getAddress()->getAddress();
        } elseif ($header instanceof MailboxListHeader) {
            foreach ($header->getAddresses() as $address) {
                if ($this->expectedValue === $address->getAddress()) {
                    return true;
                }
            }

            return false;
        }

        throw new \LogicException('Unable to test a message address on a non-address header.');
    }

    /**
     * @param RawMessage $message
     */
    protected function failureDescription($message): string
    {
        return \sprintf('the Email %s (value is %s)', $this->toString(), $message->getHeaders()->get($this->headerName)->getBodyAsString());
    }
}
