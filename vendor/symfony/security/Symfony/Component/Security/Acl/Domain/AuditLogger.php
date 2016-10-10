<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Domain;

use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\AuditLoggerInterface;

/**
 * Base audit logger implementation
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class AuditLogger implements AuditLoggerInterface
{
    /**
     * Performs some checks if logging was requested
     *
     * @param bool           $granted
     * @param EntryInterface $ace
     */
    public function logIfNeeded($granted, EntryInterface $ace)
    {
        if (!$ace instanceof AuditableEntryInterface) {
            return;
        }

        if ($granted && $ace->isAuditSuccess()) {
            $this->doLog($granted, $ace);
        } elseif (!$granted && $ace->isAuditFailure()) {
            $this->doLog($granted, $ace);
        }
    }

    /**
     * This method is only called when logging is needed
     *
     * @param bool           $granted
     * @param EntryInterface $ace
     */
    abstract protected function doLog($granted, EntryInterface $ace);
}
