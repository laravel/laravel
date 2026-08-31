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

use Stringable;

interface UserInfoInterface extends UriComponentInterface
{
    /**
     * Returns the user component part.
     */
    public function getUser(): ?string;

    /**
     * Returns the pass component part.
     */
    public function getPass(): ?string;

    /**
     * Returns an associative array containing all the User Info components.
     *
     * The returned a hashmap similar to PHP's parse_url return value
     *
     * @link https://tools.ietf.org/html/rfc3986
     *
     * @return array{user: ?string, pass : ?string}
     */
    public function components(): array;

    /**
     * Returns an instance with the specified user and/or pass.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified new username
     * otherwise it returns the same instance unchanged.
     *
     * A variable equal to null is equivalent to removing the complete user information.
     */
    public function withUser(Stringable|string|null $username): self;

    /**
     * Returns an instance with the specified user and/or pass.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified password if the user is specified
     * otherwise it returns the same instance unchanged.
     *
     * An empty user is equivalent to removing the user information.
     */
    public function withPass(Stringable|string|null $password): self;
}
