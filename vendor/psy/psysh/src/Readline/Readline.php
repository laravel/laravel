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
 * An interface abstracting the various readline_* functions.
 */
interface Readline
{
    /**
     * @param string|false $historyFile
     * @param int|null     $historySize
     * @param bool|null    $eraseDups
     */
    public function __construct($historyFile = null, $historySize = 0, $eraseDups = false);

    /**
     * Check whether this Readline class is supported by the current system.
     */
    public static function isSupported(): bool;

    /**
     * Check whether this Readline class supports bracketed paste.
     */
    public static function supportsBracketedPaste(): bool;

    /**
     * Add a line to the command history.
     *
     * @param string $line
     *
     * @return bool Success
     */
    public function addHistory(string $line): bool;

    /**
     * Clear the command history.
     *
     * @return bool Success
     */
    public function clearHistory(): bool;

    /**
     * List the command history.
     *
     * @return string[]
     */
    public function listHistory(): array;

    /**
     * Read the command history.
     *
     * @return bool Success
     */
    public function readHistory(): bool;

    /**
     * Read a single line of input from the user.
     *
     * @param string|null $prompt
     *
     * @return false|string
     */
    public function readline(?string $prompt = null);

    /**
     * Redraw readline to redraw the display.
     */
    public function redisplay();

    /**
     * Write the command history to a file.
     *
     * @return bool Success
     */
    public function writeHistory(): bool;
}
