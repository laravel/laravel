<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Formatter;

/**
 * Reflector formatter interface.
 */
interface ReflectorFormatter
{
    /**
     * @param \Reflector $reflector
     */
    public static function format(\Reflector $reflector): string;
}
