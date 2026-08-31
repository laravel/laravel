<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Contracts;

use JsonSerializable;
use Stringable;

/**
 * @method static when(callable|bool $condition, callable $onSuccess, ?callable $onFail = null) conditionally return a new instance
 * @method bool equals(mixed $value) tells whether the submitted value is equal to the current instance value
 */
interface UriComponentInterface extends JsonSerializable, Stringable
{
    /**
     * Returns the instance string representation.
     *
     * If the instance is defined, the value returned MUST be percent-encoded,
     * but MUST NOT double-encode any characters. To determine what characters
     * to encode, please refer to RFC 3986, Sections 2 and 3.
     *
     * If the instance is not defined null is returned
     */
    public function value(): ?string;

    /**
     * Returns the instance string representation.
     *
     * If the instance is defined, the value returned MUST be percent-encoded,
     * but MUST NOT double-encode any characters. To determine what characters
     * to encode, please refer to RFC 3986, Sections 2 and 3.
     *
     * If the instance is not defined, an empty string is returned
     */
    public function toString(): string;

    /**
     * Returns the instance string representation.
     *
     * If the instance is defined, the value returned MUST be percent-encoded,
     * but MUST NOT double-encode any characters. To determine what characters
     * to encode, please refer to RFC 3986, Sections 2 and 3.
     *
     * If the instance is not defined, an empty string is returned
     */
    public function __toString(): string;

    /**
     * Returns the instance json representation.
     *
     * If the instance is defined, the value returned MUST be percent-encoded,
     * but MUST NOT double-encode any characters. To determine what characters
     * to encode, please refer to RFC 3986 or RFC 1738.
     *
     * If the instance is not defined, null is returned
     */
    public function jsonSerialize(): ?string;

    /**
     * Returns the instance string representation with its optional URI delimiters.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode any
     * characters. To determine what characters to encode, please refer to RFC 3986,
     * Sections 2 and 3.
     *
     * If the instance is not defined, an empty string is returned
     */
    public function getUriComponent(): string;
}
