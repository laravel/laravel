<?php

declare(strict_types=1);

namespace Dotenv\Parser;

final class Lexer
{
    /**
     * The regex for each type of token.
     */
    private const PATTERNS = [
        '[\r\n]{1,1000}', '[^\S\r\n]{1,1000}', '\\\\', '\'', '"', '\\#', '\\$', '([^(\s\\\\\'"\\#\\$)]|\\(|\\)){1,1000}',
    ];

    /**
     * This class is a singleton.
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    private function __construct()
    {
        //
    }

    /**
     * Convert content into a token stream.
     *
     * Multibyte string processing is not needed here, and nether is error
     * handling, for performance reasons.
     *
     * @param string $content
     *
     * @return \Generator<string>
     */
    public static function lex(string $content)
    {
        static $regex;

        if ($regex === null) {
            $regex = '(('.\implode(')|(', self::PATTERNS).'))A';
        }

        $offset = 0;

        while (isset($content[$offset])) {
            if (!\preg_match($regex, $content, $matches, 0, $offset)) {
                throw new \Error(\sprintf('Lexer encountered unexpected character [%s].', $content[$offset]));
            }

            $offset += \strlen($matches[0]);

            yield $matches[0];
        }
    }
}
