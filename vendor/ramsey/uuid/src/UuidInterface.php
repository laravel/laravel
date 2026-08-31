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

use JsonSerializable;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Serializable;
use Stringable;

/**
 * A UUID is a universally unique identifier adhering to an agreed-upon representation format and standard for generation
 *
 * @immutable
 */
interface UuidInterface extends
    DeprecatedUuidInterface,
    JsonSerializable,
    Serializable,
    Stringable
{
    /**
     * Returns -1, 0, or 1 if the UUID is less than, equal to, or greater than the other UUID
     *
     * The first of two UUIDs is greater than the second if the most significant field in which the UUIDs differ is
     * greater for the first UUID.
     *
     * @param UuidInterface $other The UUID to compare
     *
     * @return int<-1,1> -1, 0, or 1 if the UUID is less than, equal to, or greater than $other
     */
    public function compareTo(UuidInterface $other): int;

    /**
     * Returns true if the UUID is equal to the provided object
     *
     * The result is true if and only if the argument is not null, is a UUID object, has the same variant, and contains
     * the same value, bit-for-bit, as the UUID.
     *
     * @param object | null $other An object to test for equality with this UUID
     *
     * @return bool True if the other object is equal to this UUID
     */
    public function equals(?object $other): bool;

    /**
     * Returns the binary string representation of the UUID
     *
     * @return non-empty-string
     *
     * @pure
     */
    public function getBytes(): string;

    /**
     * Returns the fields that comprise this UUID
     */
    public function getFields(): FieldsInterface;

    /**
     * Returns the hexadecimal representation of the UUID
     */
    public function getHex(): Hexadecimal;

    /**
     * Returns the integer representation of the UUID
     */
    public function getInteger(): IntegerObject;

    /**
     * Returns the string standard representation of the UUID as a URN
     *
     * @link http://en.wikipedia.org/wiki/Uniform_Resource_Name Uniform Resource Name
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#section-4 RFC 9562, 4. UUID Format
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#section-7 RFC 9562, 7. IANA Considerations
     * @link https://www.rfc-editor.org/rfc/rfc4122.html#section-3 RFC 4122, 3. Namespace Registration Template
     */
    public function getUrn(): string;

    /**
     * Returns the string standard representation of the UUID
     *
     * @return non-empty-string
     *
     * @pure
     */
    public function toString(): string;

    /**
     * Casts the UUID to the string standard representation
     *
     * @return non-empty-string
     *
     * @pure
     */
    public function __toString(): string;
}
