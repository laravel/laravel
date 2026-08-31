<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid;

use DateTimeInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Validator\ValidatorInterface;

/**
 * UuidFactoryInterface defines the common functionality all `UuidFactory` instances must implement
 */
interface UuidFactoryInterface
{
    /**
     * Creates a UUID from a byte string
     *
     * @param string $bytes A binary string
     *
     * @return UuidInterface A UuidInterface instance created from a binary string representation
     *
     * @pure
     */
    public function fromBytes(string $bytes): UuidInterface;

    /**
     * Creates a UUID from a DateTimeInterface instance
     *
     * @param DateTimeInterface $dateTime The date and time
     * @param Hexadecimal | null $node A 48-bit number representing the hardware address
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a version 1 UUID created from a DateTimeInterface instance
     */
    public function fromDateTime(
        DateTimeInterface $dateTime,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null,
    ): UuidInterface;

    /**
     * Creates a UUID from a 128-bit integer string
     *
     * @param string $integer String representation of 128-bit integer
     *
     * @return UuidInterface A UuidInterface instance created from the string representation of a 128-bit integer
     *
     * @pure
     */
    public function fromInteger(string $integer): UuidInterface;

    /**
     * Creates a UUID from the string standard representation
     *
     * @param string $uuid A hexadecimal string
     *
     * @return UuidInterface A UuidInterface instance created from a hexadecimal string representation
     *
     * @pure
     */
    public function fromString(string $uuid): UuidInterface;

    /**
     * Returns the validator used by the factory
     */
    public function getValidator(): ValidatorInterface;

    /**
     * Returns a version 1 (Gregorian time) UUID from a host ID, sequence number, and the current time
     *
     * @param Hexadecimal | int | string | null $node A 48-bit number representing the hardware address; this number may
     *     be represented as an integer or a hexadecimal string
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a version 1 UUID
     */
    public function uuid1($node = null, ?int $clockSeq = null): UuidInterface;

    /**
     * Returns a version 2 (DCE Security) UUID from a local domain, local identifier, host ID, clock sequence, and the
     * current time
     *
     * @param int $localDomain The local domain to use when generating bytes, according to DCE Security
     * @param IntegerObject | null $localIdentifier The local identifier for the given domain; this may be a UID or GID
     *     on POSIX systems, if the local domain is a person or group, or it may be a site-defined identifier if the
     *     local domain is org
     * @param Hexadecimal | null $node A 48-bit number representing the hardware address
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a version 2 UUID
     */
    public function uuid2(
        int $localDomain,
        ?IntegerObject $localIdentifier = null,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null,
    ): UuidInterface;

    /**
     * Returns a version 3 (name-based) UUID based on the MD5 hash of a namespace ID and a name
     *
     * @param UuidInterface | string $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a version 3 UUID
     *
     * @pure
     */
    public function uuid3($ns, string $name): UuidInterface;

    /**
     * Returns a version 4 (random) UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a version 4 UUID
     */
    public function uuid4(): UuidInterface;

    /**
     * Returns a version 5 (name-based) UUID based on the SHA-1 hash of a namespace ID and a name
     *
     * @param UuidInterface | string $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a version 5 UUID
     *
     * @pure
     */
    public function uuid5($ns, string $name): UuidInterface;

    /**
     * Returns a version 6 (reordered Gregorian time) UUID from a host ID, sequence number, and the current time
     *
     * @param Hexadecimal | null $node A 48-bit number representing the hardware address
     * @param int | null $clockSeq A 14-bit number used to help avoid duplicates that could arise when the clock is set
     *     backwards in time or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a version 6 UUID
     */
    public function uuid6(?Hexadecimal $node = null, ?int $clockSeq = null): UuidInterface;
}
