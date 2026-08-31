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
use Symfony\Component\Mime\Header\UnstructuredHeader;
use Symfony\Component\Mime\RawMessage;

final class EmailHeaderSame extends Constraint
{
    public function __construct(
        private string $headerName,
        private string $expectedValue,
    ) {
    }

    public function toString(): string
    {
        return \sprintf('has header "%s" with value "%s"', $this->headerName, $this->expectedValue);
    }

    /**
     * @param RawMessage $message
     */
    protected function matches($message): bool
    {
        if (RawMessage::class === $message::class) {
            throw new \LogicException('Unable to test a message header on a RawMessage instance.');
        }

        return $this->expectedValue === $this->getHeaderValue($message);
    }

    /**
     * @param RawMessage $message
     */
    protected function failureDescription($message): string
    {
        return \sprintf('the Email %s (value is %s)', $this->toString(), $this->getHeaderValue($message) ?? 'null');
    }

    private function getHeaderValue($message): ?string
    {
        if (null === $header = $message->getHeaders()->get($this->headerName)) {
            return null;
        }

        return $header instanceof UnstructuredHeader ? $header->getValue() : $header->getBodyAsString();
    }
}
