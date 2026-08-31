<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Formatter;

use Psy\Exception\RuntimeException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * A pretty-printer for code.
 */
class CodeFormatter implements ReflectorFormatter
{
    const LINE_MARKER = '  <urgent>></urgent> ';
    const NO_LINE_MARKER = '    ';

    const HIGHLIGHT_DEFAULT = 'default';
    const HIGHLIGHT_KEYWORD = 'keyword';

    const HIGHLIGHT_PUBLIC = 'public';
    const HIGHLIGHT_PROTECTED = 'protected';
    const HIGHLIGHT_PRIVATE = 'private';
    const HIGHLIGHT_CLASS = 'class';

    const HIGHLIGHT_BOOL = 'bool';
    const HIGHLIGHT_CONST = 'const';
    const HIGHLIGHT_NUMBER = 'number';
    const HIGHLIGHT_STRING = 'string';
    const HIGHLIGHT_ARRAY_KEY = 'array_key';
    const HIGHLIGHT_COMMENT = 'code_comment';
    const HIGHLIGHT_INLINE_HTML = 'inline_html';

    private const PHP_SNIPPET_PREFIX = '<?php ';
    private const CLASS_REFERENCE_KEYWORDS = [\T_NEW, \T_INSTANCEOF, \T_CATCH];

    private const BASE_TOKEN_MAP = [
        // Not highlighted
        \T_OPEN_TAG           => self::HIGHLIGHT_DEFAULT,
        \T_OPEN_TAG_WITH_ECHO => self::HIGHLIGHT_DEFAULT,
        \T_CLOSE_TAG          => self::HIGHLIGHT_DEFAULT,
        \T_STRING             => self::HIGHLIGHT_DEFAULT,
        \T_VARIABLE           => self::HIGHLIGHT_DEFAULT,
        \T_NS_SEPARATOR       => self::HIGHLIGHT_DEFAULT,

        // Visibility
        \T_PUBLIC    => self::HIGHLIGHT_PUBLIC,
        \T_PROTECTED => self::HIGHLIGHT_PROTECTED,
        \T_PRIVATE   => self::HIGHLIGHT_PRIVATE,

        // Constants
        \T_DIR      => self::HIGHLIGHT_CONST,
        \T_FILE     => self::HIGHLIGHT_CONST,
        \T_METHOD_C => self::HIGHLIGHT_CONST,
        \T_NS_C     => self::HIGHLIGHT_CONST,
        \T_LINE     => self::HIGHLIGHT_CONST,
        \T_CLASS_C  => self::HIGHLIGHT_CONST,
        \T_FUNC_C   => self::HIGHLIGHT_CONST,
        \T_TRAIT_C  => self::HIGHLIGHT_CONST,

        // Types
        \T_DNUMBER                  => self::HIGHLIGHT_NUMBER,
        \T_LNUMBER                  => self::HIGHLIGHT_NUMBER,
        \T_ENCAPSED_AND_WHITESPACE  => self::HIGHLIGHT_STRING,
        \T_CONSTANT_ENCAPSED_STRING => self::HIGHLIGHT_STRING,

        // Comments
        \T_COMMENT     => self::HIGHLIGHT_COMMENT,
        \T_DOC_COMMENT => self::HIGHLIGHT_COMMENT,

        // @todo something better here?
        \T_INLINE_HTML => self::HIGHLIGHT_INLINE_HTML,
    ];

    private const KEYWORD_TOKENS = [
        \T_ABSTRACT,
        \T_ARRAY,
        \T_AS,
        \T_BREAK,
        \T_CALLABLE,
        \T_CASE,
        \T_CATCH,
        \T_CLONE,
        \T_CONTINUE,
        \T_DECLARE,
        \T_DEFAULT,
        \T_DO,
        \T_ECHO,
        \T_ELSE,
        \T_ELSEIF,
        \T_EMPTY,
        \T_ENDDECLARE,
        \T_ENDFOR,
        \T_ENDFOREACH,
        \T_ENDIF,
        \T_ENDSWITCH,
        \T_ENDWHILE,
        \T_EVAL,
        \T_EXIT,
        \T_EXTENDS,
        \T_FINAL,
        \T_FINALLY,
        \T_FN,
        \T_FOR,
        \T_FOREACH,
        \T_FUNCTION,
        \T_GLOBAL,
        \T_GOTO,
        \T_IF,
        \T_IMPLEMENTS,
        \T_INCLUDE,
        \T_INCLUDE_ONCE,
        \T_INSTANCEOF,
        \T_INSTEADOF,
        \T_INTERFACE,
        \T_ISSET,
        \T_LIST,
        \T_LOGICAL_AND,
        \T_LOGICAL_OR,
        \T_LOGICAL_XOR,
        \T_NAMESPACE,
        \T_NEW,
        \T_PRINT,
        \T_REQUIRE,
        \T_REQUIRE_ONCE,
        \T_RETURN,
        \T_STATIC,
        \T_SWITCH,
        \T_THROW,
        \T_TRAIT,
        \T_TRY,
        \T_UNSET,
        \T_USE,
        \T_VAR,
        \T_WHILE,
        \T_YIELD,
        \T_YIELD_FROM,
    ];

    /**
     * Format the code represented by $reflector for shell output.
     *
     * @param \Reflector $reflector
     *
     * @return string formatted code
     */
    public static function format(\Reflector $reflector): string
    {
        if (self::isReflectable($reflector)) {
            // @phan-suppress-next-line PhanUndeclaredMethod - getFileName/getEndLine exist on ReflectionClass/ReflectionFunctionAbstract
            if ($code = @\file_get_contents($reflector->getFileName())) {
                // @phan-suppress-next-line PhanUndeclaredMethod - getEndLine exists on ReflectionClass/ReflectionFunctionAbstract
                return self::formatCode($code, self::getStartLine($reflector), $reflector->getEndLine());
            }
        }

        throw new RuntimeException('Source code unavailable');
    }

    /**
     * Format code for shell output.
     *
     * Optionally, restrict by $startLine and $endLine line numbers, or pass $markLine to add a line marker.
     *
     * @param string   $code
     * @param int      $startLine
     * @param int|null $endLine
     * @param int|null $markLine
     *
     * @return string formatted code
     */
    public static function formatCode(string $code, int $startLine = 1, ?int $endLine = null, ?int $markLine = null): string
    {
        $spans = self::tokenizeSpans($code);
        $lines = self::splitLines($spans, $startLine, $endLine);
        $lines = self::formatLines($lines);
        $lines = self::numberLines($lines, $markLine);

        return \implode('', \iterator_to_array($lines));
    }

    /**
     * Format a PHP code block for shell output without line numbers.
     */
    public static function formatCodeBlock(string $code): string
    {
        return self::formatSpanBlock(self::tokenizeSpans($code));
    }

    /**
     * Format a PHP snippet code block for shell output without line numbers.
     *
     * The code is tokenized as PHP without displaying a synthetic opening tag.
     */
    public static function formatSnippetCodeBlock(string $code): string
    {
        return self::formatSpanBlock(self::tokenizeSnippetSpans($code));
    }

    /**
     * Format a PHP snippet into ANSI-safe lines for inline shell rendering.
     *
     * Styles are applied line-by-line so multiline tokens do not leak styling
     * across prompt boundaries.
     *
     * @return string[]
     */
    public static function formatInputLines(string $code, ?OutputFormatterInterface $formatter = null): array
    {
        $lines = [''];
        $lineIndex = 0;

        foreach (self::tokenizeSnippetSpans($code) as [$spanType, $spanText]) {
            $parts = \preg_split('/(\r\n?|\n)/', $spanText, -1, \PREG_SPLIT_DELIM_CAPTURE);
            if ($parts === false) {
                $parts = [$spanText];
            }

            foreach ($parts as $part) {
                if ($part === "\r" || $part === "\n" || $part === "\r\n") {
                    $lines[++$lineIndex] = '';
                    continue;
                }

                if ($part === '') {
                    continue;
                }

                $lines[$lineIndex] .= self::formatInputSpan($spanType, $part, $formatter);
            }
        }

        return $lines;
    }

    /**
     * Get the start line for a given Reflector.
     *
     * Tries to incorporate doc comments if possible.
     *
     * This is typehinted as \Reflector but we've narrowed the input via self::isReflectable already.
     *
     * @param \ReflectionClass|\ReflectionFunctionAbstract $reflector
     */
    private static function getStartLine(\Reflector $reflector): int
    {
        $startLine = $reflector->getStartLine();

        if ($docComment = $reflector->getDocComment()) {
            $startLine -= \preg_match_all('/(\r\n?|\n)/', $docComment) + 1;
        }

        return \max($startLine, 1);
    }

    /**
     * Split code into highlight spans.
     *
     * Tokenize via \token_get_all, then map these tokens to internal highlight types, combining
     * adjacent spans of the same highlight type.
     *
     * @todo consider switching \token_get_all() out for PHP-Parser-based formatting at some point.
     *
     * @param string $code
     *
     * @return \Generator [$spanType, $spanText] highlight spans
     */
    private static function tokenizeSpans(string $code): \Generator
    {
        $tokens = \token_get_all($code);
        $arrayKeyIndexes = self::findArrayKeyTokenIndexes($tokens);
        [$classNameIndexes, $keywordNameIndexes] = self::findClassNameTokenIndexes($tokens);
        $spanType = null;
        $buffer = '';

        foreach ($tokens as $index => $token) {
            if (isset($arrayKeyIndexes[$index])) {
                $nextType = self::HIGHLIGHT_ARRAY_KEY;
            } elseif (isset($keywordNameIndexes[$index])) {
                $nextType = self::HIGHLIGHT_KEYWORD;
            } elseif (isset($classNameIndexes[$index])) {
                $nextType = self::HIGHLIGHT_CLASS;
            } else {
                $nextType = self::nextHighlightType($token);
            }
            $spanType = $spanType ?: $nextType;

            if ($spanType !== $nextType) {
                yield [$spanType, $buffer];
                $spanType = $nextType;
                $buffer = '';
            }

            $buffer .= \is_array($token) ? $token[1] : $token;
        }

        if ($spanType !== null && $buffer !== '') {
            yield [$spanType, $buffer];
        }
    }

    /**
     * @return \Generator [$spanType, $spanText] highlight spans
     */
    private static function tokenizeSnippetSpans(string $code): \Generator
    {
        $spans = self::tokenizeSpans(self::PHP_SNIPPET_PREFIX.$code);
        $first = true;
        $prefixLength = \strlen(self::PHP_SNIPPET_PREFIX);

        foreach ($spans as [$spanType, $spanText]) {
            if ($first) {
                $spanText = (string) \substr($spanText, $prefixLength);
                $first = false;

                if ($spanText === '') {
                    continue;
                }
            }

            yield [$spanType, $spanText];
        }
    }

    private static function formatSpanBlock(\Generator $spans): string
    {
        $output = '';
        foreach (self::formatLines(self::splitLines($spans)) as $line) {
            $output .= $line;
        }

        return \rtrim($output, "\r\n");
    }

    /**
     * Given a token, compute the highlight type from the token map.
     *
     * @param array|string $token \token_get_all token
     *
     * @return string|null
     */
    private static function nextHighlightType($token)
    {
        if ($token === '"') {
            return self::HIGHLIGHT_STRING;
        }

        if (\is_array($token)) {
            if (($literalHighlight = self::literalHighlightType($token)) !== null) {
                return $literalHighlight;
            }

            $tokenMap = self::getTokenMap();
            if (\array_key_exists($token[0], $tokenMap)) {
                return $tokenMap[$token[0]];
            }
        }

        return self::HIGHLIGHT_DEFAULT;
    }

    /**
     * Build the token-to-style map once so unsupported token constants on older
     * PHP versions can be ignored safely.
     *
     * @return string[]
     */
    private static function getTokenMap(): array
    {
        static $tokenMap = null;

        if ($tokenMap !== null) {
            return $tokenMap;
        }

        $tokenMap = self::BASE_TOKEN_MAP;

        foreach (self::KEYWORD_TOKENS as $tokenType) {
            $tokenMap[$tokenType] = self::HIGHLIGHT_KEYWORD;
        }

        if (\defined('T_MATCH')) {
            $tokenMap[\constant('T_MATCH')] = self::HIGHLIGHT_KEYWORD;
        }

        return $tokenMap;
    }

    /**
     * Mark string literal tokens that are serving as array keys.
     *
     * This stays token-based instead of requiring a valid AST so it can work
     * during interactive editing on incomplete input.
     *
     * @param array<int, array|string> $tokens
     *
     * @return array<int, true>
     */
    private static function findArrayKeyTokenIndexes(array $tokens): array
    {
        $arrayKeyIndexes = [];
        $stack = [];
        $pendingArrayParen = false;

        foreach ($tokens as $index => $token) {
            if (\is_array($token)) {
                if ($token[0] === \T_ARRAY) {
                    $pendingArrayParen = true;
                }

                if ($token[0] === \T_CONSTANT_ENCAPSED_STRING && self::isArrayContext($stack) && self::nextSignificantTokenIsDoubleArrow($tokens, $index)) {
                    $arrayKeyIndexes[$index] = true;
                }

                if (!self::isIgnorableToken($token) && $token[0] !== \T_ARRAY) {
                    $pendingArrayParen = false;
                }

                continue;
            }

            if ($token === '(') {
                $stack[] = [
                    'closing'   => ')',
                    'arrayLike' => $pendingArrayParen,
                ];
                $pendingArrayParen = false;
                continue;
            }

            if ($token === '[') {
                $stack[] = [
                    'closing'   => ']',
                    'arrayLike' => true,
                ];
                $pendingArrayParen = false;
                continue;
            }

            if (($token === ')' || $token === ']') && !empty($stack)) {
                $top = \array_pop($stack);
                if (($top['closing'] ?? null) !== $token) {
                    $stack = [];
                }
            }

            if ($token !== ',' && $token !== '=>' && $token !== '=') {
                $pendingArrayParen = false;
            }
        }

        return $arrayKeyIndexes;
    }

    /**
     * @param array<int, array{closing: string, arrayLike: bool}> $stack
     */
    private static function isArrayContext(array $stack): bool
    {
        if ($stack === []) {
            return false;
        }

        $top = $stack[\count($stack) - 1];

        return !empty($top['arrayLike']);
    }

    /**
     * @param array<int, array|string> $tokens
     */
    private static function nextSignificantTokenIsDoubleArrow(array $tokens, int $index): bool
    {
        $count = \count($tokens);

        for ($i = $index + 1; $i < $count; $i++) {
            $token = $tokens[$i];

            if (self::isIgnorableToken($token)) {
                continue;
            }

            return \is_array($token) && $token[0] === \T_DOUBLE_ARROW;
        }

        return false;
    }

    /**
     * Mark tokens that are very likely to be class names from immediate syntax context.
     *
     * This is intentionally heuristic and conservative: declaration names,
     * extends/implements lists, obvious runtime class references (`new`,
     * `instanceof`, `catch`), and static references like `Foo::class`.
     *
     * On PHP 8+, qualified names arrive as T_NAME_* tokens and can be marked as
     * a single unit. On PHP 7.4, qualified names are split across T_STRING and
     * T_NS_SEPARATOR tokens, so we stitch them together only in these narrow,
     * class-specific contexts.
     *
     * @param array<int, array|string> $tokens
     *
     * @return array{0: array<int, true>, 1: array<int, true>}
     */
    private static function findClassNameTokenIndexes(array $tokens): array
    {
        $classIndexes = [];
        $keywordIndexes = [];
        $context = null;

        foreach ($tokens as $index => $token) {
            if (self::isIgnorableToken($token)) {
                continue;
            }

            if ($context !== null) {
                if (self::literalHighlightType($token) !== null) {
                    continue;
                }

                if (self::isClassNameToken($token)) {
                    if (self::isPseudoClassToken($token)) {
                        $keywordIndexes[$index] = true;
                    } else {
                        $classIndexes[$index] = true;
                    }
                    continue;
                }

                if (self::isNamespaceSeparatorToken($token)) {
                    $classIndexes[$index] = true;
                    continue;
                }

                if ($context === 'list' && ($token === ',' || $token === '&')) {
                    continue;
                }

                if ($context === 'union' && ($token === '|' || $token === '&')) {
                    continue;
                }

                if ($context === 'union' && $token === '?') {
                    continue;
                }

                $context = null;
            }

            if (!\is_array($token)) {
                continue;
            }

            $tokenType = $token[0];

            if (self::isClassDeclarationKeyword($tokenType) && !self::previousSignificantTokenIs($tokens, $index, \T_DOUBLE_COLON)) {
                $context = 'single';
                continue;
            }

            if ($tokenType === \T_EXTENDS || $tokenType === \T_IMPLEMENTS) {
                $context = 'list';
                continue;
            }

            if (\in_array($tokenType, self::CLASS_REFERENCE_KEYWORDS, true)) {
                $context = 'union';
                continue;
            }

            if ($tokenType === \T_DOUBLE_COLON) {
                self::markPreviousNameToken($tokens, $classIndexes, $keywordIndexes, $index - 1);
            }
        }

        return [$classIndexes, $keywordIndexes];
    }

    /**
     * @param array|string $token
     */
    private static function isIgnorableToken($token): bool
    {
        return \is_array($token) && ($token[0] === \T_WHITESPACE || $token[0] === \T_COMMENT || $token[0] === \T_DOC_COMMENT);
    }

    /**
     * @param array|string $token
     */
    private static function isClassNameToken($token): bool
    {
        if (!\is_array($token)) {
            return false;
        }

        if ($token[0] === \T_STRING) {
            return true;
        }

        return \in_array($token[0], self::qualifiedNameTokenTypes(), true);
    }

    /**
     * @param array|string $token
     */
    private static function isNamespaceSeparatorToken($token): bool
    {
        return \is_array($token) && $token[0] === \T_NS_SEPARATOR;
    }

    /**
     * @return int[]
     */
    private static function qualifiedNameTokenTypes(): array
    {
        static $types = null;

        if ($types !== null) {
            return $types;
        }

        $types = [];

        foreach (['T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED', 'T_NAME_RELATIVE'] as $tokenName) {
            if (\defined($tokenName)) {
                $types[] = \constant($tokenName);
            }
        }

        return $types;
    }

    private static function isClassDeclarationKeyword(int $tokenType): bool
    {
        if (\in_array($tokenType, [\T_CLASS, \T_INTERFACE, \T_TRAIT], true)) {
            return true;
        }

        return \defined('T_ENUM') && $tokenType === \constant('T_ENUM');
    }

    /**
     * @param array<int, array|string> $tokens
     */
    private static function previousSignificantTokenIs(array $tokens, int $index, int $tokenType): bool
    {
        for ($i = $index - 1; $i >= 0; $i--) {
            $token = $tokens[$i];

            if (self::isIgnorableToken($token)) {
                continue;
            }

            return \is_array($token) && $token[0] === $tokenType;
        }

        return false;
    }

    /**
     * @param array<int, array|string> $tokens
     * @param array<int, true>         $classIndexes
     * @param array<int, true>         $keywordIndexes
     */
    private static function markPreviousNameToken(array $tokens, array &$classIndexes, array &$keywordIndexes, int $index): void
    {
        $chainIndexes = [];

        for ($i = $index; $i >= 0; $i--) {
            $token = $tokens[$i];

            if (self::isIgnorableToken($token)) {
                if ($chainIndexes === []) {
                    continue;
                }

                break;
            }

            if (self::isNamespaceSeparatorToken($token) || self::isClassNameToken($token)) {
                $chainIndexes[] = $i;
                continue;
            }

            break;
        }

        if ($chainIndexes === []) {
            return;
        }

        $firstIndex = $chainIndexes[0];
        if (self::literalHighlightType($tokens[$firstIndex]) !== null) {
            return;
        }

        if (self::isPseudoClassToken($tokens[$firstIndex])) {
            $keywordIndexes[$firstIndex] = true;

            return;
        }

        foreach ($chainIndexes as $chainIndex) {
            $classIndexes[$chainIndex] = true;
        }
    }

    /**
     * @param array|string $token
     */
    private static function isPseudoClassToken($token): bool
    {
        return \is_array($token)
            && $token[0] === \T_STRING
            && \in_array(\strtolower($token[1]), ['self', 'parent', 'static'], true);
    }

    /**
     * @param array|string $token
     */
    private static function literalHighlightType($token): ?string
    {
        if (!\is_array($token) || $token[0] !== \T_STRING) {
            return null;
        }

        $value = \strtolower($token[1]);

        if ($value === 'true' || $value === 'false') {
            return self::HIGHLIGHT_BOOL;
        }

        if ($value === 'null') {
            return self::HIGHLIGHT_CONST;
        }

        return null;
    }

    /**
     * Group highlight spans into an array of lines.
     *
     * Optionally, restrict by start and end line numbers.
     *
     * @param \Generator $spans     as [$spanType, $spanText] pairs
     * @param int        $startLine
     * @param int|null   $endLine
     *
     * @return \Generator lines, each an array of [$spanType, $spanText] pairs
     */
    private static function splitLines(\Generator $spans, int $startLine = 1, ?int $endLine = null): \Generator
    {
        $lineNum = 1;
        $buffer = [];

        foreach ($spans as list($spanType, $spanText)) {
            foreach (\preg_split('/(\r\n?|\n)/', $spanText) as $index => $spanLine) {
                if ($index > 0) {
                    if ($lineNum >= $startLine) {
                        yield $lineNum => $buffer;
                    }

                    $lineNum++;
                    $buffer = [];

                    if ($endLine !== null && $lineNum > $endLine) {
                        return;
                    }
                }

                if ($spanLine !== '') {
                    $buffer[] = [$spanType, $spanLine];
                }
            }
        }

        if (!empty($buffer)) {
            yield $lineNum => $buffer;
        }
    }

    /**
     * Format lines of highlight spans for shell output.
     *
     * @param \Generator $spanLines lines, each an array of [$spanType, $spanText] pairs
     *
     * @return \Generator Formatted lines
     */
    private static function formatLines(\Generator $spanLines): \Generator
    {
        foreach ($spanLines as $lineNum => $spanLine) {
            $line = '';

            foreach ($spanLine as list($spanType, $spanText)) {
                if ($spanType === self::HIGHLIGHT_DEFAULT) {
                    $line .= OutputFormatter::escape($spanText);
                } else {
                    $line .= \sprintf('<%s>%s</%s>', $spanType, OutputFormatter::escape($spanText), $spanType);
                }
            }

            yield $lineNum => $line.\PHP_EOL;
        }
    }

    /**
     * Prepend line numbers to formatted lines.
     *
     * Lines must be in an associative array with the correct keys in order to be numbered properly.
     *
     * Optionally, pass $markLine to add a line marker.
     *
     * @param \Generator $lines    Formatted lines
     * @param int|null   $markLine
     *
     * @return \Generator Numbered, formatted lines
     */
    private static function numberLines(\Generator $lines, ?int $markLine = null): \Generator
    {
        $lines = \iterator_to_array($lines);

        // Figure out how much space to reserve for line numbers.
        \end($lines);
        $pad = \strlen(\key($lines));

        // If $markLine is before or after our line range, don't bother reserving space for the marker.
        if ($markLine !== null) {
            if ($markLine > \key($lines)) {
                $markLine = null;
            }

            \reset($lines);
            if ($markLine < \key($lines)) {
                $markLine = null;
            }
        }

        foreach ($lines as $lineNum => $line) {
            $mark = '';
            if ($markLine !== null) {
                $mark = ($markLine === $lineNum) ? self::LINE_MARKER : self::NO_LINE_MARKER;
            }

            yield \sprintf("%s<whisper>%{$pad}s:</whisper> %s", $mark, $lineNum, $line);
        }
    }

    /**
     * Check whether a Reflector instance is reflectable by this formatter.
     *
     * @phpstan-assert-if-true \ReflectionClass|\ReflectionFunctionAbstract $reflector
     *
     * @param \Reflector $reflector
     */
    private static function isReflectable(\Reflector $reflector): bool
    {
        return ($reflector instanceof \ReflectionClass || $reflector instanceof \ReflectionFunctionAbstract) && \is_file($reflector->getFileName());
    }

    private static function formatInputSpan(string $spanType, string $spanText, ?OutputFormatterInterface $formatter): string
    {
        if ($spanType === self::HIGHLIGHT_DEFAULT || $formatter === null || !$formatter->isDecorated() || !$formatter->hasStyle($spanType)) {
            return $spanText;
        }

        return $formatter->getStyle($spanType)->apply($spanText);
    }
}
