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
 * This exception is thrown whenever you change shared properties of more than
 * one ACL of the same class type concurrently.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ConcurrentModificationException extends Exception
{
}
