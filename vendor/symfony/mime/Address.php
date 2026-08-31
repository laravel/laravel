<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\MessageIDValidation;
use Egulias\EmailValidator\Validation\RFCValidation;
use Symfony\Component\Mime\Encoder\IdnAddressEncoder;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Exception\RfcComplianceException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class Address
{
    /**
     * A regex that matches a structure like 'Name <email@address.com>'.
     * It matches anything between the first < and last > as email address.
     * This allows to use a single string to construct an Address, which can be convenient to use in
     * config, and allows to have more readable config.
     * This does not try to cover all edge cases for address.
     */
    private const FROM_STRING_PATTERN = '~(?<displayName>[^<]*)<(?<addrSpec>.*)>[^>]*~';

    private static EmailValidator $validator;
    private static IdnAddressEncoder $encoder;

    private string $address;
    private string $name;

    public function __construct(string $address, string $name = '')
    {
        if (!class_exists(EmailValidator::class)) {
            throw new LogicException(\sprintf('The "%s" class cannot be used as it needs "%s". Try running "composer require egulias/email-validator".', __CLASS__, EmailValidator::class));
        }

        self::$validator ??= new EmailValidator();

        $this->address = trim($address);
        $this->name = trim(str_replace(["\n", "\r"], '', $name));

        if (preg_match('/[\x00-\x1F\x7F]/', $this->address)) {
            throw new InvalidArgumentException('Email address contains control characters.');
        }

        if (!self::$validator->isValid($this->address, class_exists(MessageIDValidation::class) ? new MessageIDValidation() : new RFCValidation())) {
            throw new RfcComplianceException(\sprintf('Email "%s" does not comply with addr-spec of RFC 2822.', $address));
        }
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEncodedAddress(): string
    {
        self::$encoder ??= new IdnAddressEncoder();

        return self::$encoder->encodeString($this->address);
    }

    public function toString(): string
    {
        return ($n = $this->getEncodedName()) ? $n.' <'.$this->getEncodedAddress().'>' : $this->getEncodedAddress();
    }

    public function getEncodedName(): string
    {
        if ('' === $this->getName()) {
            return '';
        }

        return \sprintf('"%s"', preg_replace('/"/u', '\"', $this->getName()));
    }

    public static function create(self|string $address): self
    {
        if ($address instanceof self) {
            return $address;
        }

        if (!str_contains($address, '<')) {
            return new self($address);
        }

        if (!preg_match(self::FROM_STRING_PATTERN, $address, $matches)) {
            throw new InvalidArgumentException(\sprintf('Could not parse "%s" to a "%s" instance.', $address, self::class));
        }

        return new self($matches['addrSpec'], trim($matches['displayName'], ' \'"'));
    }

    /**
     * @param array<Address|string> $addresses
     *
     * @return Address[]
     */
    public static function createArray(array $addresses): array
    {
        $addrs = [];
        foreach ($addresses as $address) {
            $addrs[] = self::create($address);
        }

        return $addrs;
    }

    /**
     * Returns true if this address' localpart contains at least one
     * non-ASCII character, and false if it is only ASCII (or empty).
     *
     * This is a helper for Envelope, which has to decide whether to
     * the SMTPUTF8 extensions (RFC 6530 and following) for any given
     * message.
     *
     * The SMTPUTF8 extension is strictly required if any address
     * contains a non-ASCII character in its localpart. If non-ASCII
     * is only used in domains (e.g. horst@freiherr-von-mühlhausen.de)
     * then it is possible to send the message using IDN encoding
     * instead of SMTPUTF8. The most common software will display the
     * message as intended.
     */
    public function hasUnicodeLocalpart(): bool
    {
        return (bool) preg_match('/[\x80-\xFF].*@/', $this->address);
    }
}
