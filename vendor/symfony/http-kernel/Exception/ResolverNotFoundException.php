<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Exception;

class ResolverNotFoundException extends \RuntimeException
{
    /**
     * @param string[] $alternatives
     */
    public function __construct(string $name, array $alternatives = [])
    {
        $msg = \sprintf('You have requested a non-existent resolver "%s".', $name);
        if ($alternatives) {
            if (1 === \count($alternatives)) {
                $msg .= ' Did you mean this: "';
            } else {
                $msg .= ' Did you mean one of these: "';
            }
            $msg .= implode('", "', $alternatives).'"?';
        }

        parent::__construct($msg);
    }
}
