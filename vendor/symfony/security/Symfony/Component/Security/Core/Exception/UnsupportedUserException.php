<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Exception;

/**
 * This exception is thrown when an account is reloaded from a provider which
 * doesn't support the passed implementation of UserInterface.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class UnsupportedUserException extends AuthenticationServiceException
{
}
