<?php declare(strict_types=1);

namespace PhpParser\Internal;

if (\PHP_VERSION_ID >= 80000) {
    class TokenPolyfill extends \PhpToken {
    }
    return;
}

/**
 * This is a polyfill for the PhpToken class introduced in PHP 8.0. We do not actually polyfill
 * PhpToken, because composer might end up picking a different polyfill implementation, which does
 * not meet our requirements.
 *
 * @internal
 */
class TokenPolyfill {
    /** @var int The ID of the token. Either a T_* constant of a character code < 256. */
    public int $id;
    /** @var string The textual content of the token. */
    public string $text;
    /** @var int The 1-based starting line of the token (or -1 if unknown). */
    public int $line;
    /** @var int The 0-based starting position of the token (or -1 if unknown). */
    public int $pos;

    /** @var array<int, bool> Tokens ignored by the PHP parser. */
    private const IGNORABLE_TOKENS = [
        \T_WHITESPACE => true,
        \T_COMMENT => true,
        \T_DOC_COMMENT => true,
        \T_OPEN_TAG => true,
    ];

    /** @var array<int, bool> Tokens that may be part of a T_NAME_* identifier. */
    private static array $identifierTokens;

    /**
     * Create a Token with the given ID and text, as well optional line and position information.
     */
    final public function __construct(int $id, string $text, int $line = -1, int $pos = -1) {
        $this->id = $id;
        $this->text = $text;
        $this->line = $line;
        $this->pos = $pos;
    }

    /**
     * Get the name of the token. For single-char tokens this will be the token character.
     * Otherwise it will be a T_* style name, or null if the token ID is unknown.
     */
    public function getTokenName(): ?string {
        if ($this->id < 256) {
            return \chr($this->id);
        }

        $name = token_name($this->id);
        return $name === 'UNKNOWN' ? null : $name;
    }

    /**
     * Check whether the token is of the given kind. The kind may be either an integer that matches
     * the token ID, a string that matches the token text, or an array of integers/strings. In the
     * latter case, the function returns true if any of the kinds in the array match.
     *
     * @param int|string|(int|string)[] $kind
     */
    public function is($kind): bool {
        if (\is_int($kind)) {
            return $this->id === $kind;
        }
        if (\is_string($kind)) {
            return $this->text === $kind;
        }
        if (\is_array($kind)) {
            foreach ($kind as $entry) {
                if (\is_int($entry)) {
                    if ($this->id === $entry) {
                        return true;
                    }
                } elseif (\is_string($entry)) {
                    if ($this->text === $entry) {
                        return true;
                    }
                } else {
                    throw new \TypeError(
                        'Argument #1 ($kind) must only have elements of type string|int, ' .
                        gettype($entry) . ' given');
                }
            }
            return false;
        }
        throw new \TypeError(
            'Argument #1 ($kind) must be of type string|int|array, ' .gettype($kind) . ' given');
    }

    /**
     * Check whether this token would be ignored by the PHP parser. Returns true for T_WHITESPACE,
     * T_COMMENT, T_DOC_COMMENT and T_OPEN_TAG, and false for everything else.
     */
    public function isIgnorable(): bool {
        return isset(self::IGNORABLE_TOKENS[$this->id]);
    }

    /**
     * Return the textual content of the token.
     */
    public function __toString(): string {
        return $this->text;
    }

    /**
     * Tokenize the given source code and return an array of tokens.
     *
     * This performs certain canonicalizations to match the PHP 8.0 token format:
     *  * Bad characters are represented using T_BAD_CHARACTER rather than omitted.
     *  * T_COMMENT does not include trailing newlines, instead the newline is part of a following
     *    T_WHITESPACE token.
     *  * Namespaced names are represented using T_NAME_* tokens.
     *
     * @return static[]
     */
    public static function tokenize(string $code, int $flags = 0): array {
        self::init();

        $tokens = [];
        $line = 1;
        $pos = 0;
        $origTokens = \token_get_all($code, $flags);

        $numTokens = \count($origTokens);
        for ($i = 0; $i < $numTokens; $i++) {
            $token = $origTokens[$i];
            if (\is_string($token)) {
                if (\strlen($token) === 2) {
                    // b" and B" are tokenized as single-char tokens, even though they aren't.
                    $tokens[] = new static(\ord('"'), $token, $line, $pos);
                    $pos += 2;
                } else {
                    $tokens[] = new static(\ord($token), $token, $line, $pos);
                    $pos++;
                }
            } else {
                $id = $token[0];
                $text = $token[1];

                // Emulate PHP 8.0 comment format, which does not include trailing whitespace anymore.
                if ($id === \T_COMMENT && \substr($text, 0, 2) !== '/*' &&
                    \preg_match('/(\r\n|\n|\r)$/D', $text, $matches)
                ) {
                    $trailingNewline = $matches[0];
                    $text = \substr($text, 0, -\strlen($trailingNewline));
                    $tokens[] = new static($id, $text, $line, $pos);
                    $pos += \strlen($text);

                    if ($i + 1 < $numTokens && $origTokens[$i + 1][0] === \T_WHITESPACE) {
                        // Move trailing newline into following T_WHITESPACE token, if it already exists.
                        $origTokens[$i + 1][1] = $trailingNewline . $origTokens[$i + 1][1];
                        $origTokens[$i + 1][2]--;
                    } else {
                        // Otherwise, we need to create a new T_WHITESPACE token.
                        $tokens[] = new static(\T_WHITESPACE, $trailingNewline, $line, $pos);
                        $line++;
                        $pos += \strlen($trailingNewline);
                    }
                    continue;
                }

                // Emulate PHP 8.0 T_NAME_* tokens, by combining sequences of T_NS_SEPARATOR and
                // T_STRING into a single token.
                if (($id === \T_NS_SEPARATOR || isset(self::$identifierTokens[$id]))) {
                    $newText = $text;
                    $lastWasSeparator = $id === \T_NS_SEPARATOR;
                    for ($j = $i + 1; $j < $numTokens; $j++) {
                        if ($lastWasSeparator) {
                            if (!isset(self::$identifierTokens[$origTokens[$j][0]])) {
                                break;
                            }
                            $lastWasSeparator = false;
                        } else {
                            if ($origTokens[$j][0] !== \T_NS_SEPARATOR) {
                                break;
                            }
                            $lastWasSeparator = true;
                        }
                        $newText .= $origTokens[$j][1];
                    }
                    if ($lastWasSeparator) {
                        // Trailing separator is not part of the name.
                        $j--;
                        $newText = \substr($newText, 0, -1);
                    }
                    if ($j > $i + 1) {
                        if ($id === \T_NS_SEPARATOR) {
                            $id = \T_NAME_FULLY_QUALIFIED;
                        } elseif ($id === \T_NAMESPACE) {
                            $id = \T_NAME_RELATIVE;
                        } else {
                            $id = \T_NAME_QUALIFIED;
                        }
                        $tokens[] = new static($id, $newText, $line, $pos);
                        $pos += \strlen($newText);
                        $i = $j - 1;
                        continue;
                    }
                }

                $tokens[] = new static($id, $text, $line, $pos);
                $line += \substr_count($text, "\n");
                $pos += \strlen($text);
            }
        }
        return $tokens;
    }

    /** Initialize private static state needed by tokenize(). */
    private static function init(): void {
        if (isset(self::$identifierTokens)) {
            return;
        }

        // Based on semi_reserved production.
        self::$identifierTokens = \array_fill_keys([
            \T_STRING,
            \T_STATIC, \T_ABSTRACT, \T_FINAL, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC, \T_READONLY,
            \T_INCLUDE, \T_INCLUDE_ONCE, \T_EVAL, \T_REQUIRE, \T_REQUIRE_ONCE, \T_LOGICAL_OR, \T_LOGICAL_XOR, \T_LOGICAL_AND,
            \T_INSTANCEOF, \T_NEW, \T_CLONE, \T_EXIT, \T_IF, \T_ELSEIF, \T_ELSE, \T_ENDIF, \T_ECHO, \T_DO, \T_WHILE,
            \T_ENDWHILE, \T_FOR, \T_ENDFOR, \T_FOREACH, \T_ENDFOREACH, \T_DECLARE, \T_ENDDECLARE, \T_AS, \T_TRY, \T_CATCH,
            \T_FINALLY, \T_THROW, \T_USE, \T_INSTEADOF, \T_GLOBAL, \T_VAR, \T_UNSET, \T_ISSET, \T_EMPTY, \T_CONTINUE, \T_GOTO,
            \T_FUNCTION, \T_CONST, \T_RETURN, \T_PRINT, \T_YIELD, \T_LIST, \T_SWITCH, \T_ENDSWITCH, \T_CASE, \T_DEFAULT,
            \T_BREAK, \T_ARRAY, \T_CALLABLE, \T_EXTENDS, \T_IMPLEMENTS, \T_NAMESPACE, \T_TRAIT, \T_INTERFACE, \T_CLASS,
            \T_CLASS_C, \T_TRAIT_C, \T_FUNC_C, \T_METHOD_C, \T_LINE, \T_FILE, \T_DIR, \T_NS_C, \T_HALT_COMPILER, \T_FN,
            \T_MATCH,
        ], true);
    }
}
