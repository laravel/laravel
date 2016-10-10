<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\User;

/**
 * UserCheckerInterface checks user account when authentication occurs.
 *
 * This should not be used to make authentication decisions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface UserCheckerInterface
{
    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user a UserInterface instance
     */
    public function checkPreAuth(UserInterface $user);

    /**
     * Checks the user account after authentication.
     *
     * @param UserInterface $user a UserInterface instance
     */
    public function checkPostAuth(UserInterface $user);
}
