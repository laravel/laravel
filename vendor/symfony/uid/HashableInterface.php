<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid;

if (interface_exists(\Ds\Key::class)) {
    /**
     * @internal
     */
    interface HashableInterface extends \Ds\Key
    {
        public function equals(mixed $other): bool;

        public function hash(): string;
    }
} elseif (interface_exists(\Ds\Hashable::class)) {
    /**
     * @internal
     */
    interface HashableInterface extends \Ds\Hashable
    {
        public function hash(): string;
    }
} else {
    /**
     * @internal
     */
    interface HashableInterface
    {
        public function equals(mixed $other): bool;

        public function hash(): string;
    }
}
