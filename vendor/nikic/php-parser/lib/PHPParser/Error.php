<?php

class PHPParser_Error extends RuntimeException
{
    protected $rawMessage;
    protected $rawLine;

    /**
     * Creates an Exception signifying a parse error.
     *
     * @param string $message Error message
     * @param int    $line    Error line in PHP file
     */
    public function __construct($message, $line = -1) {
        $this->rawMessage = (string) $message;
        $this->rawLine    = (int) $line;
        $this->updateMessage();
    }

    /**
     * Gets the error message
     *
     * @return string Error message
     */
    public function getRawMessage() {
        return $this->rawMessage;
    }

    /**
     * Sets the line of the PHP file the error occurred in.
     *
     * @param string $message Error message
     */
    public function setRawMessage($message) {
        $this->rawMessage = (string) $message;
        $this->updateMessage();
    }

    /**
     * Gets the error line in the PHP file.
     *
     * @return int Error line in the PHP file
     */
    public function getRawLine() {
        return $this->rawLine;
    }

    /**
     * Sets the line of the PHP file the error occurred in.
     *
     * @param int $line Error line in the PHP file
     */
    public function setRawLine($line) {
        $this->rawLine = (int) $line;
        $this->updateMessage();
    }

    /**
     * Updates the exception message after a change to rawMessage or rawLine.
     */
    protected function updateMessage() {
        $this->message = $this->rawMessage;

        if (-1 === $this->rawLine) {
            $this->message .= ' on unknown line';
        } else {
            $this->message .= ' on line ' . $this->rawLine;
        }
    }
}