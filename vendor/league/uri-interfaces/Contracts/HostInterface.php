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

/**
 * @method string|null encoded() returns RFC3986 encoded host
 */
interface HostInterface extends UriComponentInterface
{
    /**
     * Returns the ascii representation.
     */
    public function toAscii(): ?string;

    /**
     * Returns the unicode representation.
     */
    public function toUnicode(): ?string;

    /**
     * Returns the IP version.
     *
     * If the host is a not an IP this method will return null
     */
    public function getIpVersion(): ?string;

    /**
     * Returns the IP component If the Host is an IP address.
     *
     * If the host is a not an IP this method will return null
     */
    public function getIp(): ?string;

    /**
     * Tells whether the host is a domain name.
     */
    public function isDomain(): bool;

    /**
     * Tells whether the host is an IP Address.
     */
    public function isIp(): bool;

    /**
     * Tells whether the host is a registered name.
     */
    public function isRegisteredName(): bool;
}
