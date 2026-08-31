<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Exception;

/**
 * An interface for Psy Exceptions.
 */
interface Exception
{
    /**
     * This is the only thing, really...
     *
     * Return a raw (unformatted) version of the message.
     *
     * @return string
     */
    public function getRawMessage();
}
