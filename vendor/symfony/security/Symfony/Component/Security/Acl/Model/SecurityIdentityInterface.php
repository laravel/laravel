<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Model;

/**
 * This interface provides an additional level of indirection, so that
 * we can work with abstracted versions of security objects and do
 * not have to save the entire objects.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface SecurityIdentityInterface
{
    /**
     * This method is used to compare two security identities in order to
     * not rely on referential equality.
     *
     * @param SecurityIdentityInterface $identity
     */
    public function equals(SecurityIdentityInterface $identity);
}
