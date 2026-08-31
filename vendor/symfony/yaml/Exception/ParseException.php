<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml\Exception;

/**
 * Exception class thrown when an error occurs during parsing.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ParseException extends RuntimeException
{
    /**
     * @param string      $rawMessage The error message
     * @param int         $parsedLine The line where the error occurred
     * @param string|null $snippet    The snippet of code near the problem
     * @param string|null $parsedFile The file name where the error occurred
     */
    public function __construct(
        private string $rawMessage,
        private int $parsedLine = -1,
        private ?string $snippet = null,
        private ?string $parsedFile = null,
        ?\Throwable $previous = null,
    ) {
        $this->updateRepr();

        parent::__construct($this->message, 0, $previous);
    }

    /**
     * Gets the snippet of code near the error.
     */
    public function getSnippet(): string
    {
        return $this->snippet;
    }

    /**
     * Sets the snippet of code near the error.
     */
    public function setSnippet(string $snippet): void
    {
        $this->snippet = $snippet;

        $this->updateRepr();
    }

    /**
     * Gets the filename where the error occurred.
     *
     * This method returns null if a string is parsed.
     */
    public function getParsedFile(): string
    {
        return $this->parsedFile;
    }

    /**
     * Sets the filename where the error occurred.
     */
    public function setParsedFile(string $parsedFile): void
    {
        $this->parsedFile = $parsedFile;

        $this->updateRepr();
    }

    /**
     * Gets the line where the error occurred.
     */
    public function getParsedLine(): int
    {
        return $this->parsedLine;
    }

    /**
     * Sets the line where the error occurred.
     */
    public function setParsedLine(int $parsedLine): void
    {
        $this->parsedLine = $parsedLine;

        $this->updateRepr();
    }

    private function updateRepr(): void
    {
        $this->message = $this->rawMessage;

        $dot = false;
        if (str_ends_with($this->message, '.')) {
            $this->message = substr($this->message, 0, -1);
            $dot = true;
        }

        if (null !== $this->parsedFile) {
            $this->message .= \sprintf(' in %s', json_encode($this->parsedFile, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE));
        }

        if ($this->parsedLine >= 0) {
            $this->message .= \sprintf(' at line %d', $this->parsedLine);
        }

        if ($this->snippet) {
            $this->message .= \sprintf(' (near "%s")', $this->snippet);
        }

        if ($dot) {
            $this->message .= '.';
        }
    }
}
