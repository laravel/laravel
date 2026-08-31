<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\PHP_VERSION_ID < 80100) {
    #[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION | Attribute::TARGET_CLASS_CONSTANT)]
    final class Deprecated
    {
        /**
         * @readonly
         */
        public ?string $message;

        /**
         * @readonly
         */
        public ?string $since;

        public function __construct(?string $message = null, ?string $since = null)
        {
            $this->message = $message;
            $this->since = $since;
        }
    }
} elseif (\PHP_VERSION_ID < 80400) {
    require dirname(__DIR__).'/Deprecated.php';
}
