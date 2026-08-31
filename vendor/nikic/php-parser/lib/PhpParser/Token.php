<?php declare(strict_types=1);

namespace PhpParser;

/**
 * A PHP token. On PHP 8.0 this extends from PhpToken.
 */
class Token extends Internal\TokenPolyfill {
    /** Get (exclusive) zero-based end position of the token. */
    public function getEndPos(): int {
        return $this->pos + \strlen($this->text);
    }

    /** Get 1-based end line number of the token. */
    public function getEndLine(): int {
        return $this->line + \substr_count($this->text, "\n");
    }
}
