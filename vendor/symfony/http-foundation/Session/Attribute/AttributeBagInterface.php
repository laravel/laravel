<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Attribute;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * Attributes store.
 *
 * @author Drak <drak@zikula.org>
 */
interface AttributeBagInterface extends SessionBagInterface
{
    /**
     * Checks if an attribute is defined.
     */
    public function has(string $name): bool;

    /**
     * Returns an attribute.
     */
    public function get(string $name, mixed $default = null): mixed;

    /**
     * Sets an attribute.
     */
    public function set(string $name, mixed $value): void;

    /**
     * Returns attributes.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    public function replace(array $attributes): void;

    /**
     * Removes an attribute.
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function remove(string $name): mixed;
}
