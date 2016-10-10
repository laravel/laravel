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
 * ACEs can implement this interface if they support auditing capabilities.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface AuditableEntryInterface extends EntryInterface
{
    /**
     * Whether auditing for successful grants is turned on
     *
     * @return bool
     */
    public function isAuditFailure();

    /**
     * Whether auditing for successful denies is turned on
     *
     * @return bool
     */
    public function isAuditSuccess();
}
