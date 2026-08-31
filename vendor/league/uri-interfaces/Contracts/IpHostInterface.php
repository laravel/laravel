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

interface IpHostInterface extends HostInterface
{
    /**
     * Tells whether the host is an IPv4 address.
     */
    public function isIpv4(): bool;

    /**
     * Tells whether the host is an IPv6 address.
     */
    public function isIpv6(): bool;

    /**
     * Tells whether the host is an IPv6 address.
     */
    public function isIpFuture(): bool;

    /**
     * Tells whether the host has a ZoneIdentifier.
     *
     * @see http://tools.ietf.org/html/rfc6874#section-4
     */
    public function hasZoneIdentifier(): bool;

    /**
     * Returns a host without its zone identifier according to RFC6874.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance without the host zone identifier according to RFC6874
     *
     * @see http://tools.ietf.org/html/rfc6874#section-4
     */
    public function withoutZoneIdentifier(): self;
}
