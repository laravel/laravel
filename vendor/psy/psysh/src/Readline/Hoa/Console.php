<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Psy\Readline\Hoa;

/**
 * Class \Hoa\Console.
 *
 * A set of utils and helpers about the console.
 */
class Console
{
    /**
     * Pipe mode: FIFO.
     */
    const IS_FIFO = 0;

    /**
     * Pipe mode: character.
     */
    const IS_CHARACTER = 1;

    /**
     * Pipe mode: directory.
     */
    const IS_DIRECTORY = 2;

    /**
     * Pipe mode: block.
     */
    const IS_BLOCK = 3;

    /**
     * Pipe mode: regular.
     */
    const IS_REGULAR = 4;

    /**
     * Pipe mode: link.
     */
    const IS_LINK = 5;

    /**
     * Pipe mode: socket.
     */
    const IS_SOCKET = 6;

    /**
     * Pipe mode: whiteout.
     */
    const IS_WHITEOUT = 7;

    /**
     * Advanced interaction is on.
     */
    private static $_advanced = null;

    /**
     * Previous STTY configuration.
     */
    private static $_old = null;

    /**
     * Mode.
     */
    protected static $_mode = [];

    /**
     * Input.
     */
    protected static $_input = null;

    /**
     * Output.
     */
    protected static $_output = null;

    /**
     * Tput.
     */
    protected static $_tput = null;

    /**
     * Prepare the environment for advanced interactions.
     */
    public static function advancedInteraction(bool $force = false): bool
    {
        if (null !== self::$_advanced) {
            return self::$_advanced;
        }

        if (\defined('PHP_WINDOWS_VERSION_PLATFORM')) {
            return self::$_advanced = false;
        }

        if (false === $force &&
            true === \defined('STDIN') &&
            false === self::isDirect(\STDIN)) {
            return self::$_advanced = false;
        }

        self::$_old = ConsoleProcessus::execute('stty -g < /dev/tty', false);
        ConsoleProcessus::execute('stty -echo -icanon min 1 time 0 < /dev/tty', false);

        return self::$_advanced = true;
    }

    /**
     * Restore previous interaction options.
     */
    public static function restoreInteraction()
    {
        if (null === self::$_old) {
            return;
        }

        ConsoleProcessus::execute('stty '.self::$_old.' < /dev/tty', false);

        return;
    }

    /**
     * Get mode of a certain pipe.
     * Inspired by sys/stat.h.
     */
    public static function getMode($pipe = \STDIN): int
    {
        $_pipe = (int) $pipe;

        if (isset(self::$_mode[$_pipe])) {
            return self::$_mode[$_pipe];
        }

        $stat = \fstat($pipe);

        switch ($stat['mode'] & 0170000) {
            // named pipe (fifo).
            case 0010000:
                $mode = self::IS_FIFO;

                break;

            // character special.
            case 0020000:
                $mode = self::IS_CHARACTER;

                break;

            // directory.
            case 0040000:
                $mode = self::IS_DIRECTORY;

                break;

            // block special.
            case 0060000:
                $mode = self::IS_BLOCK;

                break;

            // regular.
            case 0100000:
                $mode = self::IS_REGULAR;

                break;

            // symbolic link.
            case 0120000:
                $mode = self::IS_LINK;

                 break;

            // socket.
            case 0140000:
                $mode = self::IS_SOCKET;

                break;

            // whiteout.
            case 0160000:
                $mode = self::IS_WHITEOUT;

                break;

            default:
                $mode = -1;
        }

        return self::$_mode[$_pipe] = $mode;
    }

    /**
     * Check whether a certain pipe is a character device (keyboard, screen
     * etc.).
     * For example:
     *     $ php Mode.php
     * In this case, self::isDirect(STDOUT) will return true.
     */
    public static function isDirect($pipe): bool
    {
        return self::IS_CHARACTER === self::getMode($pipe);
    }

    /**
     * Check whether a certain pipe is a pipe.
     * For example:
     *     $ php Mode.php | foobar
     * In this case, self::isPipe(STDOUT) will return true.
     */
    public static function isPipe($pipe): bool
    {
        return self::IS_FIFO === self::getMode($pipe);
    }

    /**
     * Check whether a certain pipe is a redirection.
     * For example:
     *     $ php Mode.php < foobar
     * In this case, self::isRedirection(STDIN) will return true.
     */
    public static function isRedirection($pipe): bool
    {
        $mode = self::getMode($pipe);

        return
            self::IS_REGULAR === $mode ||
            self::IS_DIRECTORY === $mode ||
            self::IS_LINK === $mode ||
            self::IS_SOCKET === $mode ||
            self::IS_BLOCK === $mode;
    }

    /**
     * Set input layer.
     */
    public static function setInput(ConsoleInput $input)
    {
        $old = static::$_input;
        static::$_input = $input;

        return $old;
    }

    /**
     * Get input layer.
     */
    public static function getInput(): ConsoleInput
    {
        if (null === static::$_input) {
            static::$_input = new ConsoleInput();
        }

        return static::$_input;
    }

    /**
     * Set output layer.
     */
    public static function setOutput(ConsoleOutput $output)
    {
        $old = static::$_output;
        static::$_output = $output;

        return $old;
    }

    /**
     * Get output layer.
     */
    public static function getOutput(): ConsoleOutput
    {
        if (null === static::$_output) {
            static::$_output = new ConsoleOutput();
        }

        return static::$_output;
    }

    /**
     * Set tput.
     */
    public static function setTput(ConsoleTput $tput)
    {
        $old = static::$_tput;
        static::$_tput = $tput;

        return $old;
    }

    /**
     * Get the current tput instance of the current process.
     */
    public static function getTput(): ConsoleTput
    {
        if (null === static::$_tput) {
            static::$_tput = new ConsoleTput();
        }

        return static::$_tput;
    }

    /**
     * Check whether we are running behind TMUX(1).
     */
    public static function isTmuxRunning(): bool
    {
        return isset($_SERVER['TMUX']);
    }
}

/*
 * Restore interaction.
 */
\register_shutdown_function([Console::class, 'restoreInteraction']);
