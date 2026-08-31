<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeAnalysis;

use PhpParser\Error;
use Psy\Readline\Interactive\Helper\TokenHelper;

/**
 * Cached analysis data for a code buffer snapshot.
 */
class BufferAnalysis
{
    private string $code;

    /** @var array<int, array|string> */
    private array $tokens;

    /** @var array<int, array{start: int, end: int}> */
    private array $tokenPositions;

    /** @var array<int, mixed>|null */
    private ?array $ast;
    private ?Error $lastError;

    /**
     * @param array<int, array|string>                $tokens
     * @param array<int, array{start: int, end: int}> $tokenPositions
     * @param array<int, mixed>|null                  $ast
     */
    public function __construct(string $code, array $tokens, array $tokenPositions, ?array $ast, ?Error $lastError)
    {
        $this->code = $code;
        $this->tokens = $tokens;
        $this->tokenPositions = $tokenPositions;
        $this->ast = $ast;
        $this->lastError = $lastError;
    }

    /**
     * Get the token_get_all() tokens for the buffer.
     *
     * @return array<int, array|string>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Get start/end code-point positions for each token.
     *
     * @return array<int, array{start: int, end: int}>
     */
    public function getTokenPositions(): array
    {
        return $this->tokenPositions;
    }

    /**
     * Get the parsed AST, or null if parsing failed.
     *
     * @return array<int, mixed>|null
     */
    public function getAst(): ?array
    {
        return $this->ast;
    }

    /**
     * Get the last parse error, if any.
     */
    public function getLastError(): ?Error
    {
        return $this->lastError;
    }

    /**
     * Check whether the buffer has balanced (), [] and {} pairs.
     */
    public function hasBalancedBrackets(): bool
    {
        $stack = [];
        $pairs = ['(' => ')', '[' => ']', '{' => '}'];

        foreach ($this->tokens as $token) {
            if (\is_array($token)) {
                if (\in_array($token[0], [\T_CURLY_OPEN, \T_DOLLAR_OPEN_CURLY_BRACES], true)) {
                    $stack[] = '{';
                }

                continue;
            }

            if (isset($pairs[$token])) {
                $stack[] = $token;
            } elseif (\in_array($token, $pairs, true)) {
                if (empty($stack)) {
                    return false;
                }

                $last = \array_pop($stack);
                if ($pairs[$last] !== $token) {
                    return false;
                }
            }
        }

        return empty($stack);
    }

    /**
     * Check whether the buffer ends with an operator that requires more input.
     */
    public function hasTrailingOperator(): bool
    {
        return TokenHelper::hasTrailingOperator($this->tokens);
    }

    /**
     * Check whether the token stream ends inside a string or comment context.
     */
    public function endsInOpenStringOrComment(): bool
    {
        if ($this->tokens === []) {
            return false;
        }

        $last = $this->tokens[\count($this->tokens) - 1];

        return $last === '"' || $last === '`' ||
            (\is_array($last) && \in_array($last[0], [\T_ENCAPSED_AND_WHITESPACE, \T_START_HEREDOC, \T_COMMENT], true));
    }

    /**
     * Check whether the buffer ends with a control structure header that still needs a body.
     */
    public function hasControlStructureWithoutBody(): bool
    {
        $trimmed = \rtrim($this->code);

        if (\preg_match('/\b(if|while|for|foreach|elseif)\s*\(.*\)\s*$/', $trimmed)) {
            $lastParen = \strrpos($trimmed, ')');
            if ($lastParen !== false) {
                $afterParen = \trim(\substr($trimmed, $lastParen + 1));
                if ($afterParen === '') {
                    if ($this->lastError !== null && !$this->isEOFError($this->lastError)) {
                        return false;
                    }

                    return true;
                }
            }
        }

        $isElseAfterBrace = \preg_match('/\}\s*else\s*$/', $trimmed);
        $isBareElse = \preg_match('/^\s*else\s*$/', $trimmed);

        if ($isElseAfterBrace || $isBareElse) {
            if ($isBareElse && $this->lastError !== null && !$this->isEOFError($this->lastError)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Check whether the last parse error is an unexpected-EOF error.
     */
    public function hasEOFError(): bool
    {
        return $this->lastError !== null && $this->isEOFError($this->lastError);
    }

    private function isEOFError(Error $error): bool
    {
        $msg = $error->getRawMessage();

        return ($msg === 'Unexpected token EOF') || (\strpos($msg, 'Syntax error, unexpected EOF') !== false);
    }
}
