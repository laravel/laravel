<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Util;

/**
 * Interface that needs to be implemented by all secure random number generators.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface SecureRandomInterface
{
    /**
     * Generates the specified number of secure random bytes.
     *
     * @param int     $nbBytes
     *
     * @return string
     */
    public function nextBytes($nbBytes);
}
