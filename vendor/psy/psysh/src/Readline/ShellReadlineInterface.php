<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline;

use Psy\ShellAware;

/**
 * Narrow shell-facing readline contract shared by supported readline adapters.
 */
interface ShellReadlineInterface extends Readline, ShellAware
{
    /**
     * Set whether to require semicolons on all statements.
     */
    public function setRequireSemicolons(bool $require): void;
}
