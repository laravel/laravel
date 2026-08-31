<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

/**
 * CodeCleanerAware interface.
 *
 * This interface is used to pass the Shell's CodeCleaner into commands which
 * require access to name resolution via use statements and namespace context.
 */
interface CodeCleanerAware
{
    /**
     * Set the CodeCleaner instance.
     */
    public function setCodeCleaner(CodeCleaner $cleaner);
}
