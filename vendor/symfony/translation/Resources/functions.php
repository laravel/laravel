<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

if (!\function_exists(t::class)) {
    /**
     * @author Nate Wiebe <nate@northern.co>
     */
    function t(string $message, array $parameters = [], ?string $domain = null): TranslatableMessage
    {
        return new TranslatableMessage($message, $parameters, $domain);
    }
}
