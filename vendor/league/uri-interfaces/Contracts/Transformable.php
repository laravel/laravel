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

interface Transformable
{
    /**
     * Apply a transformation to this instance and return a new instance.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance of the same type.
     *
     * @param callable(static): static $callback
     */
    public function transform(callable $callback): static;
}
