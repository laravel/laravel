<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Header;

/**
 * A Date MIME Header.
 *
 * @author Chris Corbyn
 */
final class DateHeader extends AbstractHeader
{
    private \DateTimeImmutable $dateTime;

    public function __construct(string $name, \DateTimeInterface $date)
    {
        parent::__construct($name);

        $this->setDateTime($date);
    }

    /**
     * @param \DateTimeInterface $body
     */
    public function setBody(mixed $body): void
    {
        $this->setDateTime($body);
    }

    public function getBody(): \DateTimeImmutable
    {
        return $this->getDateTime();
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    /**
     * Set the date-time of the Date in this Header.
     *
     * If a DateTime instance is provided, it is converted to DateTimeImmutable.
     */
    public function setDateTime(\DateTimeInterface $dateTime): void
    {
        $this->dateTime = \DateTimeImmutable::createFromInterface($dateTime);
    }

    public function getBodyAsString(): string
    {
        return $this->dateTime->format(\DateTimeInterface::RFC2822);
    }
}
