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
 * This exception is thrown when we cannot locate an ACL for a passed
 * ObjectIdentity implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AclNotFoundException extends Exception
{
}
