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
 * Interface for audit loggers
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface AuditLoggerInterface
{
    /**
     * This method is called whenever access is granted, or denied, and
     * administrative mode is turned off.
     *
     * @param bool           $granted
     * @param EntryInterface $ace
     */
    public function logIfNeeded($granted, EntryInterface $ace);
}
