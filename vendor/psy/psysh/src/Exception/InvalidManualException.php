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
 * An exception for invalid manual files.
 */
class InvalidManualException extends RuntimeException
{
    private string $manualFile;

    /**
     * @param string          $message    Error message
     * @param string          $manualFile Path to the invalid manual file
     * @param int             $code       (default: 0)
     * @param \Throwable|null $previous   (default: null)
     */
    public function __construct(string $message, string $manualFile, int $code = 0, ?\Throwable $previous = null)
    {
        $this->manualFile = $manualFile;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the path to the invalid manual file.
     */
    public function getManualFile(): string
    {
        return $this->manualFile;
    }
}
