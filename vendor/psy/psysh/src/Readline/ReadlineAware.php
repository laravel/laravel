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

/**
 * ReadlineAware interface.
 *
 * This interface is used to pass the Shell's readline implementation into
 * commands which depend on it.
 */
interface ReadlineAware
{
    /**
     * Set the Readline service.
     */
    public function setReadline(Readline $readline);
}
