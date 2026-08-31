<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Input;

/**
 * A simple class used internally by PsySH to represent silent input.
 *
 * Silent input is generally used for non-user-generated code, such as the
 * rewritten user code run by sudo command. Silent input isn't echoed before
 * evaluating, and it's not added to the readline history.
 */
class SilentInput
{
    private string $inputString;

    /**
     * Constructor.
     *
     * @param string $inputString
     */
    public function __construct(string $inputString)
    {
        $this->inputString = $inputString;
    }

    /**
     * To. String.
     */
    public function __toString(): string
    {
        return $this->inputString;
    }
}
