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
 * ContextAware interface.
 *
 * This interface is used to pass the Shell's context into commands and such
 * which require access to the current scope variables.
 */
interface ContextAware
{
    /**
     * Set the Context reference.
     */
    public function setContext(Context $context);
}
