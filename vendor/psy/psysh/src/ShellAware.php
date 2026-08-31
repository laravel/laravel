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
 * ShellAware interface.
 *
 * This interface is used to pass the Shell into components that need access
 * to registered commands and other shell capabilities.
 */
interface ShellAware
{
    /**
     * Set the Shell reference.
     */
    public function setShell(Shell $shell): void;
}
