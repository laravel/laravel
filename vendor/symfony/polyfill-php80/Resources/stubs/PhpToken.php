<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\PHP_VERSION_ID < 80000 && extension_loaded('tokenizer')) {
    class PhpToken extends Symfony\Polyfill\Php80\PhpToken
    {
    }
}
