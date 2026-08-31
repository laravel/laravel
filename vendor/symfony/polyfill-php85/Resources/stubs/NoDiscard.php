<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\PHP_VERSION_ID < 80500) {
    #[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
    final class NoDiscard
    {
        public ?string $message;

        public function __construct(?string $message = null)
        {
            $this->message = $message;
        }
    }
}
