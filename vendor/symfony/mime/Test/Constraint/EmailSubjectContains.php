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
use Symfony\Component\Mime\Email;

final class EmailSubjectContains extends Constraint
{
    public function __construct(
        private readonly string $expectedSubjectValue,
    ) {
    }

    public function toString(): string
    {
        return \sprintf('contains subject with value "%s"', $this->expectedSubjectValue);
    }

    protected function matches($other): bool
    {
        if (!$other instanceof Email) {
            throw new \LogicException('Can only test a message subject on an Email instance.');
        }

        return str_contains((string) $other->getSubject(), $this->expectedSubjectValue);
    }

    protected function failureDescription($other): string
    {
        $message = 'The email subject '.$this->toString();
        if ($other instanceof Email) {
            $message .= \sprintf('. The subject was: "%s"', $other->getSubject() ?? '<empty>');
        }

        return $message;
    }
}
