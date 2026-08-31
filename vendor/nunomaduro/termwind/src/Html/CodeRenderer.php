<?php

declare(strict_types=1);

namespace Termwind\Html;

use Termwind\Components\Element;
use Termwind\Termwind;
use Termwind\ValueObjects\Node;

/**
 * @internal
 */
final class CodeRenderer
{
    public const TOKEN_DEFAULT = 'token_default';

    public const TOKEN_COMMENT = 'token_comment';

    public const TOKEN_STRING = 'token_string';

    public const TOKEN_HTML = 'token_html';

    public const TOKEN_KEYWORD = 'token_keyword';

    public const ACTUAL_LINE_MARK = 'actual_line_mark';

    public const LINE_NUMBER = 'line_number';

    private const ARROW_SYMBOL_UTF8 = '➜';

    private const DELIMITER_UTF8 = '▕ '; // '▶';

    private const LINE_NUMBER_DIVIDER = 'line_divider';

    private const MARKED_LINE_NUMBER = 'marked_line';

    private const WIDTH = 3;

    /**
     * Holds the theme.
     *
     * @var array<string, string>
     */
    private const THEME = [
        self::TOKEN_STRING => 'text-gray',
        self::TOKEN_COMMENT => 'text-gray italic',
        self::TOKEN_KEYWORD => 'text-magenta strong',
        self::TOKEN_DEFAULT => 'strong',
        self::TOKEN_HTML => 'text-blue strong',

        self::ACTUAL_LINE_MARK => 'text-red strong',
        self::LINE_NUMBER => 'text-gray',
        self::MARKED_LINE_NUMBER => 'italic strong',
        self::LINE_NUMBER_DIVIDER => 'text-gray',
    ];

    private string $delimiter = self::DELIMITER_UTF8;

    private string $arrow = self::ARROW_SYMBOL_UTF8;

    private const NO_MARK = '    ';

    /**
     * Highlights HTML content from a given node and converts to the content element.
     */
    public function toElement(Node $node): Element
    {
        $line = max((int) $node->getAttribute('line'), 0);
        $startLine = max((int) $node->getAttribute('start-line'), 1);

        $html = $node->getHtml();
        $lines = explode("\n", $html);
        $extraSpaces = $this->findExtraSpaces($lines);

        if ($extraSpaces !== '') {
            $lines = array_map(static function (string $line) use ($extraSpaces): string {
                return str_starts_with($line, $extraSpaces) ? substr($line, strlen($extraSpaces)) : $line;
            }, $lines);
            $html = implode("\n", $lines);
        }

        $tokenLines = $this->getHighlightedLines(trim($html, "\n"), $startLine);
        $lines = $this->colorLines($tokenLines);
        $lines = $this->lineNumbers($lines, $line);

        return Termwind::div(trim($lines, "\n"));
    }

    /**
     * Finds extra spaces which should be removed from HTML.
     *
     * @param  array<int, string>  $lines
     */
    private function findExtraSpaces(array $lines): string
    {
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            if (preg_replace('/\s+/', '', $line) === '') {
                return $line;
            }
        }

        return '';
    }

    /**
     * Returns content split into lines with numbers.
     *
     * @return array<int, array<int, array{0: string, 1: non-empty-string}>>
     */
    private function getHighlightedLines(string $source, int $startLine): array
    {
        $source = str_replace(["\r\n", "\r"], "\n", $source);
        $tokens = $this->tokenize($source);

        return $this->splitToLines($tokens, $startLine - 1);
    }

    /**
     * Splits content into tokens.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function tokenize(string $source): array
    {
        $tokens = token_get_all($source);

        $output = [];
        $currentType = null;
        $newType = self::TOKEN_KEYWORD;
        $buffer = '';

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] !== T_WHITESPACE) {
                    $newType = match ($token[0]) {
                        T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLOSE_TAG, T_STRING, T_VARIABLE,
                        T_DIR, T_FILE, T_METHOD_C, T_DNUMBER, T_LNUMBER, T_NS_C,
                        T_LINE, T_CLASS_C, T_FUNC_C, T_TRAIT_C => self::TOKEN_DEFAULT,
                        T_COMMENT, T_DOC_COMMENT => self::TOKEN_COMMENT,
                        T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING => self::TOKEN_STRING,
                        T_INLINE_HTML => self::TOKEN_HTML,
                        default => self::TOKEN_KEYWORD
                    };
                }
            } else {
                $newType = $token === '"' ? self::TOKEN_STRING : self::TOKEN_KEYWORD;
            }

            if ($currentType === null) {
                $currentType = $newType;
            }

            if ($currentType !== $newType) {
                $output[] = [$currentType, $buffer];
                $buffer = '';
                $currentType = $newType;
            }

            $buffer .= is_array($token) ? $token[1] : $token;
        }

        $output[] = [$newType, $buffer];

        return $output;
    }

    /**
     * Splits tokens into lines.
     *
     * @param  array<int, array{0: string, 1: string}>  $tokens
     * @return array<int, array<int, array{0: string, 1: non-empty-string}>>
     */
    private function splitToLines(array $tokens, int $startLine): array
    {
        $lines = [];

        $line = [];
        foreach ($tokens as $token) {
            foreach (explode("\n", $token[1]) as $count => $tokenLine) {
                if ($count > 0) {
                    $lines[$startLine++] = $line;
                    $line = [];
                }

                if ($tokenLine === '') {
                    continue;
                }

                $line[] = [$token[0], $tokenLine];
            }
        }

        $lines[$startLine++] = $line;

        return $lines;
    }

    /**
     * Applies colors to tokens according to a color schema.
     *
     * @param  array<int, array<int, array{0: string, 1: non-empty-string}>>  $tokenLines
     * @return array<int, string>
     */
    private function colorLines(array $tokenLines): array
    {
        $lines = [];

        foreach ($tokenLines as $lineCount => $tokenLine) {
            $line = '';
            foreach ($tokenLine as $token) {
                [$tokenType, $tokenValue] = $token;
                $line .= $this->styleToken($tokenType, $tokenValue);
            }

            $lines[$lineCount] = $line;
        }

        return $lines;
    }

    /**
     * Prepends line numbers into lines.
     *
     * @param  array<int, string>  $lines
     */
    private function lineNumbers(array $lines, int $markLine): string
    {
        $lastLine = (int) array_key_last($lines);
        $lineLength = strlen((string) ($lastLine + 1));
        $lineLength = $lineLength < self::WIDTH ? self::WIDTH : $lineLength;

        $snippet = '';
        $mark = '  '.$this->arrow.' ';
        foreach ($lines as $i => $line) {
            $coloredLineNumber = $this->coloredLineNumber(self::LINE_NUMBER, $i, $lineLength);

            if ($markLine !== 0) {
                $snippet .= ($markLine === $i + 1
                    ? $this->styleToken(self::ACTUAL_LINE_MARK, $mark)
                    : self::NO_MARK
                );

                $coloredLineNumber = ($markLine === $i + 1 ?
                    $this->coloredLineNumber(self::MARKED_LINE_NUMBER, $i, $lineLength) :
                    $coloredLineNumber
                );
            }

            $snippet .= $coloredLineNumber;
            $snippet .= $this->styleToken(self::LINE_NUMBER_DIVIDER, $this->delimiter);
            $snippet .= $line.PHP_EOL;
        }

        return $snippet;
    }

    /**
     * Formats line number and applies color according to a color schema.
     */
    private function coloredLineNumber(string $token, int $lineNumber, int $length): string
    {
        return $this->styleToken(
            $token, str_pad((string) ($lineNumber + 1), $length, ' ', STR_PAD_LEFT)
        );
    }

    /**
     * Formats string and applies color according to a color schema.
     */
    private function styleToken(string $token, string $string): string
    {
        return (string) Termwind::span($string, self::THEME[$token]);
    }
}
