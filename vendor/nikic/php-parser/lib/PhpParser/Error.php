<?php declare(strict_types=1);

namespace PhpParser;

class Error extends \RuntimeException {
    protected string $rawMessage;
    /** @var array<string, mixed> */
    protected array $attributes;

    /**
     * Creates an Exception signifying a parse error.
     *
     * @param string $message Error message
     * @param array<string, mixed> $attributes Attributes of node/token where error occurred
     */
    public function __construct(string $message, array $attributes = []) {
        $this->rawMessage = $message;
        $this->attributes = $attributes;
        $this->updateMessage();
    }

    /**
     * Gets the error message
     *
     * @return string Error message
     */
    public function getRawMessage(): string {
        return $this->rawMessage;
    }

    /**
     * Gets the line the error starts in.
     *
     * @return int Error start line
     * @phpstan-return -1|positive-int
     */
    public function getStartLine(): int {
        return $this->attributes['startLine'] ?? -1;
    }

    /**
     * Gets the line the error ends in.
     *
     * @return int Error end line
     * @phpstan-return -1|positive-int
     */
    public function getEndLine(): int {
        return $this->attributes['endLine'] ?? -1;
    }

    /**
     * Gets the attributes of the node/token the error occurred at.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * Sets the attributes of the node/token the error occurred at.
     *
     * @param array<string, mixed> $attributes
     */
    public function setAttributes(array $attributes): void {
        $this->attributes = $attributes;
        $this->updateMessage();
    }

    /**
     * Sets the line of the PHP file the error occurred in.
     *
     * @param string $message Error message
     */
    public function setRawMessage(string $message): void {
        $this->rawMessage = $message;
        $this->updateMessage();
    }

    /**
     * Sets the line the error starts in.
     *
     * @param int $line Error start line
     */
    public function setStartLine(int $line): void {
        $this->attributes['startLine'] = $line;
        $this->updateMessage();
    }

    /**
     * Returns whether the error has start and end column information.
     *
     * For column information enable the startFilePos and endFilePos in the lexer options.
     */
    public function hasColumnInfo(): bool {
        return isset($this->attributes['startFilePos'], $this->attributes['endFilePos']);
    }

    /**
     * Gets the start column (1-based) into the line where the error started.
     *
     * @param string $code Source code of the file
     */
    public function getStartColumn(string $code): int {
        if (!$this->hasColumnInfo()) {
            throw new \RuntimeException('Error does not have column information');
        }

        return $this->toColumn($code, $this->attributes['startFilePos']);
    }

    /**
     * Gets the end column (1-based) into the line where the error ended.
     *
     * @param string $code Source code of the file
     */
    public function getEndColumn(string $code): int {
        if (!$this->hasColumnInfo()) {
            throw new \RuntimeException('Error does not have column information');
        }

        return $this->toColumn($code, $this->attributes['endFilePos']);
    }

    /**
     * Formats message including line and column information.
     *
     * @param string $code Source code associated with the error, for calculation of the columns
     *
     * @return string Formatted message
     */
    public function getMessageWithColumnInfo(string $code): string {
        return sprintf(
            '%s from %d:%d to %d:%d', $this->getRawMessage(),
            $this->getStartLine(), $this->getStartColumn($code),
            $this->getEndLine(), $this->getEndColumn($code)
        );
    }

    /**
     * Converts a file offset into a column.
     *
     * @param string $code Source code that $pos indexes into
     * @param int $pos 0-based position in $code
     *
     * @return int 1-based column (relative to start of line)
     */
    private function toColumn(string $code, int $pos): int {
        if ($pos > strlen($code)) {
            throw new \RuntimeException('Invalid position information');
        }

        $lineStartPos = strrpos($code, "\n", $pos - strlen($code));
        if (false === $lineStartPos) {
            $lineStartPos = -1;
        }

        return $pos - $lineStartPos;
    }

    /**
     * Updates the exception message after a change to rawMessage or rawLine.
     */
    protected function updateMessage(): void {
        $this->message = $this->rawMessage;

        if (-1 === $this->getStartLine()) {
            $this->message .= ' on unknown line';
        } else {
            $this->message .= ' on line ' . $this->getStartLine();
        }
    }
}
