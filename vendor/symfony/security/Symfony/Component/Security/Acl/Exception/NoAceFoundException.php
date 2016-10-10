<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Exception;

/**
 * This exception is thrown when we cannot locate an ACE that matches the
 * combination of permission masks and security identities.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class NoAceFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('No applicable ACE was found.');
    }
}
