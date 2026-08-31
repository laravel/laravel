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
 * `Hoa\Exception\Idle` is the mother exception class of libraries. The only
 * difference between `Hoa\Exception\Idle` and its direct children
 * `Hoa\Exception` is that the latter fires events after beeing constructed.
 */
class ExceptionIdle extends \Exception
{
    /**
     * Delay processing on arguments.
     */
    protected $_tmpArguments = null;

    /**
     * List of arguments to format message.
     */
    protected $_arguments = null;

    /**
     * Backtrace.
     */
    protected $_trace = null;

    /**
     * Previous exception if any.
     */
    protected $_previous = null;

    /**
     * Original exception message.
     */
    protected $_rawMessage = null;

    /**
     * Allocates a new exception.
     *
     * An exception is built with a formatted message, a code (an ID) and an
     * array that contains the list of formatted strings for the message. If
     * chaining, we can add a previous exception.
     */
    public function __construct(
        string $message,
        int $code = 0,
        $arguments = [],
        ?\Exception $previous = null
    ) {
        $this->_tmpArguments = $arguments;
        parent::__construct($message, $code, $previous);
        $this->_rawMessage = $message;
        $this->message = @\vsprintf($message, $this->getArguments());

        return;
    }

    /**
     * Returns the backtrace.
     *
     * Do not use `Exception::getTrace` any more.
     */
    public function getBacktrace()
    {
        if (null === $this->_trace) {
            $this->_trace = $this->getTrace();
        }

        return $this->_trace;
    }

    /**
     * Returns the previous exception if any.
     *
     * Do not use `Exception::getPrevious` any more.
     */
    public function getPreviousThrow()
    {
        if (null === $this->_previous) {
            $this->_previous = $this->getPrevious();
        }

        return $this->_previous;
    }

    /**
     * Returns the arguments of the message.
     */
    public function getArguments()
    {
        if (null === $this->_arguments) {
            $arguments = $this->_tmpArguments;

            if (!\is_array($arguments)) {
                $arguments = [$arguments];
            }

            foreach ($arguments as &$value) {
                if (null === $value) {
                    $value = '(null)';
                }
            }

            $this->_arguments = $arguments;
            unset($this->_tmpArguments);
        }

        return $this->_arguments;
    }

    /**
     * Returns the raw message.
     */
    public function getRawMessage(): string
    {
        return $this->_rawMessage;
    }

    /**
     * Returns the message already formatted.
     */
    public function getFormattedMessage(): string
    {
        return $this->getMessage();
    }

    /**
     * Returns the source of the exception (class, method, function, main etc.).
     */
    public function getFrom(): string
    {
        $trace = $this->getBacktrace();
        $from = '{main}';

        if (!empty($trace)) {
            $t = $trace[0];
            $from = '';

            if (isset($t['class'])) {
                $from .= $t['class'].'::';
            }

            if (isset($t['function'])) {
                $from .= $t['function'].'()';
            }
        }

        return $from;
    }

    /**
     * Raises an exception as a string.
     */
    public function raise(bool $includePrevious = false): string
    {
        $message = $this->getFormattedMessage();
        $trace = $this->getBacktrace();
        $file = '/dev/null';
        $line = -1;
        $pre = $this->getFrom();

        if (!empty($trace)) {
            $file = $trace['file'] ?? null;
            $line = $trace['line'] ?? null;
        }

        $pre .= ': ';

        try {
            $out =
                $pre.'('.$this->getCode().') '.$message."\n".
                'in '.$this->getFile().' at line '.
                $this->getLine().'.';
        } catch (\Exception $e) {
            $out =
                $pre.'('.$this->getCode().') '.$message."\n".
                'in '.$file.' around line '.$line.'.';
        }

        if (true === $includePrevious &&
            null !== $previous = $this->getPreviousThrow()) {
            $out .=
                "\n\n".'    ⬇'."\n\n".
                'Nested exception ('.\get_class($previous).'):'."\n".
                ($previous instanceof self
                    ? $previous->raise(true)
                    : $previous->getMessage());
        }

        return $out;
    }

    /**
     * Catches uncaught exception (only `Hoa\Exception\Idle` and children).
     */
    public static function uncaught(\Throwable $exception)
    {
        if (!($exception instanceof self)) {
            throw $exception;
        }

        while (0 < \ob_get_level()) {
            \ob_end_flush();
        }

        echo 'Uncaught exception ('.\get_class($exception).'):'."\n".
            $exception->raise(true);
    }

    /**
     * String representation of object.
     */
    public function __toString(): string
    {
        return $this->raise();
    }

    /**
     * Enables uncaught exception handler.
     *
     * This is restricted to Hoa's exceptions only.
     */
    public static function enableUncaughtHandler(bool $enable = true)
    {
        if (false === $enable) {
            return \restore_exception_handler();
        }

        return \set_exception_handler(function ($exception) {
            return self::uncaught($exception);
        });
    }
}
