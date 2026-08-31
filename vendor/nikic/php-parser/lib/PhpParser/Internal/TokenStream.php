<?php declare(strict_types=1);

namespace PhpParser\Internal;

use PhpParser\Token;

/**
 * Provides operations on token streams, for use by pretty printer.
 *
 * @internal
 */
class TokenStream {
    /** @var Token[] Tokens (in PhpToken::tokenize() format) */
    private array $tokens;
    /** @var int[] Map from position to indentation */
    private array $indentMap;

    /**
     * Create token stream instance.
     *
     * @param Token[] $tokens Tokens in PhpToken::tokenize() format
     */
    public function __construct(array $tokens, int $tabWidth) {
        $this->tokens = $tokens;
        $this->indentMap = $this->calcIndentMap($tabWidth);
    }

    /**
     * Whether the given position is immediately surrounded by parenthesis.
     *
     * @param int $startPos Start position
     * @param int $endPos End position
     */
    public function haveParens(int $startPos, int $endPos): bool {
        return $this->haveTokenImmediatelyBefore($startPos, '(')
            && $this->haveTokenImmediatelyAfter($endPos, ')');
    }

    /**
     * Whether the given position is immediately surrounded by braces.
     *
     * @param int $startPos Start position
     * @param int $endPos End position
     */
    public function haveBraces(int $startPos, int $endPos): bool {
        return ($this->haveTokenImmediatelyBefore($startPos, '{')
                || $this->haveTokenImmediatelyBefore($startPos, T_CURLY_OPEN))
            && $this->haveTokenImmediatelyAfter($endPos, '}');
    }

    /**
     * Check whether the position is directly preceded by a certain token type.
     *
     * During this check whitespace and comments are skipped.
     *
     * @param int $pos Position before which the token should occur
     * @param int|string $expectedTokenType Token to check for
     *
     * @return bool Whether the expected token was found
     */
    public function haveTokenImmediatelyBefore(int $pos, $expectedTokenType): bool {
        $tokens = $this->tokens;
        $pos--;
        for (; $pos >= 0; $pos--) {
            $token = $tokens[$pos];
            if ($token->is($expectedTokenType)) {
                return true;
            }
            if (!$token->isIgnorable()) {
                break;
            }
        }
        return false;
    }

    /**
     * Check whether the position is directly followed by a certain token type.
     *
     * During this check whitespace and comments are skipped.
     *
     * @param int $pos Position after which the token should occur
     * @param int|string $expectedTokenType Token to check for
     *
     * @return bool Whether the expected token was found
     */
    public function haveTokenImmediatelyAfter(int $pos, $expectedTokenType): bool {
        $tokens = $this->tokens;
        $pos++;
        for ($c = \count($tokens); $pos < $c; $pos++) {
            $token = $tokens[$pos];
            if ($token->is($expectedTokenType)) {
                return true;
            }
            if (!$token->isIgnorable()) {
                break;
            }
        }
        return false;
    }

    /** @param int|string|(int|string)[] $skipTokenType */
    public function skipLeft(int $pos, $skipTokenType): int {
        $tokens = $this->tokens;

        $pos = $this->skipLeftWhitespace($pos);
        if ($skipTokenType === \T_WHITESPACE) {
            return $pos;
        }

        if (!$tokens[$pos]->is($skipTokenType)) {
            // Shouldn't happen. The skip token MUST be there
            throw new \Exception('Encountered unexpected token');
        }
        $pos--;

        return $this->skipLeftWhitespace($pos);
    }

    /** @param int|string|(int|string)[] $skipTokenType */
    public function skipRight(int $pos, $skipTokenType): int {
        $tokens = $this->tokens;

        $pos = $this->skipRightWhitespace($pos);
        if ($skipTokenType === \T_WHITESPACE) {
            return $pos;
        }

        if (!$tokens[$pos]->is($skipTokenType)) {
            // Shouldn't happen. The skip token MUST be there
            throw new \Exception('Encountered unexpected token');
        }
        $pos++;

        return $this->skipRightWhitespace($pos);
    }

    /**
     * Return first non-whitespace token position smaller or equal to passed position.
     *
     * @param int $pos Token position
     * @return int Non-whitespace token position
     */
    public function skipLeftWhitespace(int $pos): int {
        $tokens = $this->tokens;
        for (; $pos >= 0; $pos--) {
            if (!$tokens[$pos]->isIgnorable()) {
                break;
            }
        }
        return $pos;
    }

    /**
     * Return first non-whitespace position greater or equal to passed position.
     *
     * @param int $pos Token position
     * @return int Non-whitespace token position
     */
    public function skipRightWhitespace(int $pos): int {
        $tokens = $this->tokens;
        for ($count = \count($tokens); $pos < $count; $pos++) {
            if (!$tokens[$pos]->isIgnorable()) {
                break;
            }
        }
        return $pos;
    }

    /** @param int|string|(int|string)[] $findTokenType */
    public function findRight(int $pos, $findTokenType): int {
        $tokens = $this->tokens;
        for ($count = \count($tokens); $pos < $count; $pos++) {
            if ($tokens[$pos]->is($findTokenType)) {
                return $pos;
            }
        }
        return -1;
    }

    /**
     * Whether the given position range contains a certain token type.
     *
     * @param int $startPos Starting position (inclusive)
     * @param int $endPos Ending position (exclusive)
     * @param int|string $tokenType Token type to look for
     * @return bool Whether the token occurs in the given range
     */
    public function haveTokenInRange(int $startPos, int $endPos, $tokenType): bool {
        $tokens = $this->tokens;
        for ($pos = $startPos; $pos < $endPos; $pos++) {
            if ($tokens[$pos]->is($tokenType)) {
                return true;
            }
        }
        return false;
    }

    public function haveTagInRange(int $startPos, int $endPos): bool {
        return $this->haveTokenInRange($startPos, $endPos, \T_OPEN_TAG)
            || $this->haveTokenInRange($startPos, $endPos, \T_CLOSE_TAG);
    }

    /**
     * Get indentation before token position.
     *
     * @param int $pos Token position
     *
     * @return int Indentation depth (in spaces)
     */
    public function getIndentationBefore(int $pos): int {
        return $this->indentMap[$pos];
    }

    /**
     * Get the code corresponding to a token offset range, optionally adjusted for indentation.
     *
     * @param int $from Token start position (inclusive)
     * @param int $to Token end position (exclusive)
     * @param int $indent By how much the code should be indented (can be negative as well)
     *
     * @return string Code corresponding to token range, adjusted for indentation
     */
    public function getTokenCode(int $from, int $to, int $indent): string {
        $tokens = $this->tokens;
        $result = '';
        for ($pos = $from; $pos < $to; $pos++) {
            $token = $tokens[$pos];
            $id = $token->id;
            $text = $token->text;
            if ($id === \T_CONSTANT_ENCAPSED_STRING || $id === \T_ENCAPSED_AND_WHITESPACE) {
                $result .= $text;
            } else {
                // TODO Handle non-space indentation
                if ($indent < 0) {
                    $result .= str_replace("\n" . str_repeat(" ", -$indent), "\n", $text);
                } elseif ($indent > 0) {
                    $result .= str_replace("\n", "\n" . str_repeat(" ", $indent), $text);
                } else {
                    $result .= $text;
                }
            }
        }
        return $result;
    }

    /**
     * Precalculate the indentation at every token position.
     *
     * @return int[] Token position to indentation map
     */
    private function calcIndentMap(int $tabWidth): array {
        $indentMap = [];
        $indent = 0;
        foreach ($this->tokens as $i => $token) {
            $indentMap[] = $indent;

            if ($token->id === \T_WHITESPACE) {
                $content = $token->text;
                $newlinePos = \strrpos($content, "\n");
                if (false !== $newlinePos) {
                    $indent = $this->getIndent(\substr($content, $newlinePos + 1), $tabWidth);
                } elseif ($i === 1 && $this->tokens[0]->id === \T_OPEN_TAG &&
                          $this->tokens[0]->text[\strlen($this->tokens[0]->text) - 1] === "\n") {
                    // Special case: Newline at the end of opening tag followed by whitespace.
                    $indent = $this->getIndent($content, $tabWidth);
                }
            }
        }

        // Add a sentinel for one past end of the file
        $indentMap[] = $indent;

        return $indentMap;
    }

    private function getIndent(string $ws, int $tabWidth): int {
        $spaces = \substr_count($ws, " ");
        $tabs = \substr_count($ws, "\t");
        assert(\strlen($ws) === $spaces + $tabs);
        return $spaces + $tabs * $tabWidth;
    }
}
