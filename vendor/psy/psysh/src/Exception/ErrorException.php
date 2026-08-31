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
 * A custom error Exception for Psy with a formatted $message.
 */
class ErrorException extends \ErrorException implements Exception
{
    private string $rawMessage;

    /**
     * Construct a Psy ErrorException.
     *
     * @param string          $message  (default: "")
     * @param int             $code     (default: 0)
     * @param int             $severity (default: 1)
     * @param string|null     $filename (default: null)
     * @param int|null        $lineno   (default: null)
     * @param \Throwable|null $previous (default: null)
     */
    public function __construct($message = '', $code = 0, $severity = 1, $filename = null, $lineno = null, ?\Throwable $previous = null)
    {
        $this->rawMessage = $message;

        if (!empty($filename) && \preg_match('{Psy[/\\\\]ExecutionLoop}', $filename)) {
            $filename = '';
        }

        switch ($severity) {
            case \E_NOTICE:
            case \E_USER_NOTICE:
                $type = 'Notice';
                break;

            case \E_WARNING:
            case \E_CORE_WARNING:
            case \E_COMPILE_WARNING:
            case \E_USER_WARNING:
                $type = 'Warning';
                break;

            case \E_DEPRECATED:
            case \E_USER_DEPRECATED:
                $type = 'Deprecated';
                break;

            case \E_RECOVERABLE_ERROR:
                $type = 'Recoverable fatal error';
                break;

            default:
                if (\PHP_VERSION_ID < 80400 && $severity === \E_STRICT) {
                    $type = 'Strict error';
                    break;
                }
                $type = 'Error';
                break;
        }

        $message = \sprintf('PHP %s:  %s%s on line %d', $type, $message, $filename ? ' in '.$filename : '', $lineno ?? 0);
        parent::__construct($message, $code, $severity, $filename ?? '', $lineno ?? 0, $previous);
    }

    /**
     * Get the raw (unformatted) message for this error.
     */
    public function getRawMessage(): string
    {
        return $this->rawMessage;
    }

    /**
     * Helper for throwing an ErrorException.
     *
     * This allows us to:
     *
     *     set_error_handler([ErrorException::class, 'throwException']);
     *
     * @throws self
     *
     * @param int    $errno   Error type
     * @param string $errstr  Message
     * @param string $errfile Filename
     * @param int    $errline Line number
     */
    public static function throwException($errno, $errstr, $errfile, $errline)
    {
        throw new self($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Create an ErrorException from an Error.
     *
     * @deprecated PsySH no longer wraps Errors
     *
     * @param \Error $e
     */
    public static function fromError(\Error $e)
    {
        @\trigger_error('PsySH no longer wraps Errors', \E_USER_DEPRECATED);
    }
}
