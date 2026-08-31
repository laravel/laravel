<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion\Refiner;

use Psy\Completion\AnalysisResult;
use Psy\Completion\CompletionKind;

/**
 * Recovers useful completion lanes when valid PHP syntax is not yet available.
 *
 * This refiner keeps completion responsive while the user is still in the
 * middle of typing an expression the parser cannot fully classify yet.
 */
class PartialInputRefiner implements AnalysisRefinerInterface
{
    /**
     * {@inheritdoc}
     */
    public function refine(AnalysisResult $analysis): AnalysisResult
    {
        if (!$analysis->parseSucceeded) {
            $partial = $this->analyzePartialInput($analysis->input, $analysis->tokens);

            return $analysis->withContext($partial->kinds, $partial->prefix, $partial->leftSide, $partial->leftSideNode);
        }

        if ($analysis->kinds !== CompletionKind::UNKNOWN) {
            return $analysis;
        }

        $partial = $this->analyzePartialInput($analysis->input, $analysis->tokens);

        return $this->shouldPreferPartialAnalysis($partial)
            ? $analysis->withContext($partial->kinds, $partial->prefix, $partial->leftSide, $partial->leftSideNode)
            : $analysis;
    }

    private function analyzePartialInput(string $input, array $tokens = []): AnalysisResult
    {
        $trimmed = \rtrim($input);
        $hasTrailingSpace = $input !== $trimmed;

        if (\preg_match('/\bnew\s+([\w\\\\]*)$/i', $input, $matches)) {
            return new AnalysisResult(CompletionKind::CLASS_NAME, $matches[1]);
        }

        if (\preg_match('/^.*[;\{\}]\s*(\w+)$/', $trimmed, $matches)) {
            if (!\preg_match('/(?:->|\?->)\w*$/', $trimmed) && !\preg_match('/::\w*$/', $trimmed)) {
                if ($hasTrailingSpace) {
                    return new AnalysisResult(CompletionKind::UNKNOWN, '');
                }

                return new AnalysisResult(CompletionKind::KEYWORD | CompletionKind::SYMBOL, $matches[1]);
            }
        }

        if (\preg_match('/(\$\w+)(?:->|\?->)([\w]*)$/', $trimmed, $matches)) {
            return new AnalysisResult(CompletionKind::OBJECT_MEMBER, $matches[2], $matches[1]);
        }

        if (\preg_match('/([\w\\\\]*\\\\)$/', $trimmed, $matches)) {
            return new AnalysisResult(CompletionKind::SYMBOL | CompletionKind::NAMESPACE, $matches[1]);
        }

        if (\preg_match('/([\w\\\\]+)::\$(\w*)$/', $trimmed, $matches)) {
            return new AnalysisResult(CompletionKind::STATIC_MEMBER, $matches[2], $matches[1]);
        }

        if (\preg_match('/([\w\\\\]+)::([\w]*)$/', $trimmed, $matches)) {
            return new AnalysisResult(CompletionKind::STATIC_MEMBER, $matches[2], $matches[1]);
        }

        $tokenizedObjectMember = $this->analyzeTokenizedObjectMemberAccess($input, $tokens);
        if ($tokenizedObjectMember !== null) {
            return $tokenizedObjectMember;
        }

        if (\preg_match('/\$(\w*)$/', $trimmed, $matches)) {
            return new AnalysisResult(CompletionKind::VARIABLE, $matches[1]);
        }

        if (\preg_match('/([\w\\\\]+)$/', $trimmed, $matches)) {
            if ($hasTrailingSpace) {
                return new AnalysisResult(CompletionKind::UNKNOWN, '');
            }

            return new AnalysisResult(CompletionKind::SYMBOL, $matches[1]);
        }

        return new AnalysisResult(CompletionKind::UNKNOWN, '');
    }

    private function shouldPreferPartialAnalysis(AnalysisResult $partialAnalysis): bool
    {
        return \in_array($partialAnalysis->kinds, [
            CompletionKind::VARIABLE,
            CompletionKind::OBJECT_MEMBER,
            CompletionKind::STATIC_MEMBER,
            CompletionKind::CLASS_NAME,
            CompletionKind::SYMBOL | CompletionKind::NAMESPACE,
        ], true);
    }

    private function analyzeTokenizedObjectMemberAccess(string $input, array $tokens): ?AnalysisResult
    {
        $entries = $this->flattenTokens($tokens);
        if ($entries === []) {
            return null;
        }

        $lastIndex = $this->findPreviousSignificantTokenIndex($entries, \count($entries) - 1);
        if ($lastIndex === null) {
            return null;
        }

        $prefix = '';
        $operatorIndex = $lastIndex;

        if ($this->isIdentifierToken($entries[$lastIndex])) {
            $prefix = $entries[$lastIndex]['text'];
            $operatorIndex = $this->findPreviousSignificantTokenIndex($entries, $lastIndex - 1);
            if ($operatorIndex === null) {
                return null;
            }
        }

        if (!$this->isObjectAccessOperatorToken($entries[$operatorIndex]['token'])) {
            return null;
        }

        $leftEndIndex = $this->findPreviousSignificantTokenIndex($entries, $operatorIndex - 1);
        if ($leftEndIndex === null) {
            return null;
        }

        $leftStartIndex = $this->findExpressionStartIndex($entries, $leftEndIndex);
        if ($leftStartIndex === null) {
            return null;
        }

        $leftSide = \mb_substr(
            $input,
            $entries[$leftStartIndex]['start'],
            $entries[$leftEndIndex]['end'] - $entries[$leftStartIndex]['start']
        );
        $leftSide = $this->normalizeLeftExpression($leftSide);

        if ($leftSide === '') {
            return null;
        }

        return new AnalysisResult(CompletionKind::OBJECT_MEMBER, $prefix, $leftSide);
    }

    private function flattenTokens(array $tokens): array
    {
        $entries = [];
        $position = 0;

        foreach ($tokens as $token) {
            $text = \is_array($token) ? $token[1] : $token;

            if (\is_array($token) && $token[0] === \T_OPEN_TAG) {
                continue;
            }

            $length = \mb_strlen($text);
            $entries[] = [
                'token' => $token,
                'text'  => $text,
                'start' => $position,
                'end'   => $position + $length,
            ];
            $position += $length;
        }

        return $entries;
    }

    private function findPreviousSignificantTokenIndex(array $entries, int $start): ?int
    {
        for ($i = $start; $i >= 0; $i--) {
            $token = $entries[$i]['token'];

            if (\is_array($token) && \in_array($token[0], [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT], true)) {
                continue;
            }

            return $i;
        }

        return null;
    }

    private function findExpressionStartIndex(array $entries, int $endIndex): ?int
    {
        $parenDepth = 0;
        $bracketDepth = 0;
        $braceDepth = 0;

        for ($i = $endIndex; $i >= 0; $i--) {
            $token = $entries[$i]['token'];
            $text = $entries[$i]['text'];

            if (!\is_array($token)) {
                if ($text === ')') {
                    $parenDepth++;
                    continue;
                }

                if ($text === '(') {
                    if ($parenDepth > 0) {
                        $parenDepth--;
                        continue;
                    }
                } elseif ($text === ']') {
                    $bracketDepth++;
                    continue;
                } elseif ($text === '[') {
                    if ($bracketDepth > 0) {
                        $bracketDepth--;
                        continue;
                    }
                } elseif ($text === '}') {
                    $braceDepth++;
                    continue;
                } elseif ($text === '{') {
                    if ($braceDepth > 0) {
                        $braceDepth--;
                        continue;
                    }
                }

                if ($parenDepth === 0 && $bracketDepth === 0 && $braceDepth === 0 && $this->isExpressionBoundaryToken($token)) {
                    return $this->findNextNonWhitespaceTokenIndex($entries, $i + 1);
                }

                continue;
            }

            if ($parenDepth === 0 && $bracketDepth === 0 && $braceDepth === 0 && $this->isExpressionBoundaryToken($token)) {
                return $this->findNextNonWhitespaceTokenIndex($entries, $i + 1);
            }
        }

        return $this->findNextNonWhitespaceTokenIndex($entries, 0);
    }

    private function findNextNonWhitespaceTokenIndex(array $entries, int $start): ?int
    {
        for ($i = $start; $i < \count($entries); $i++) {
            $token = $entries[$i]['token'];

            if (\is_array($token) && \in_array($token[0], [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT], true)) {
                continue;
            }

            return $i;
        }

        return null;
    }

    private function isExpressionBoundaryToken($token): bool
    {
        if (!\is_array($token)) {
            return \in_array($token, [';', '{', '}', ',', '=', '+', '-', '*', '/', '%', '.', '?', ':', '!', '&', '|', '^', '<', '>'], true);
        }

        return \in_array($token[0], [
            \T_DOUBLE_ARROW,
            \T_BOOLEAN_AND,
            \T_BOOLEAN_OR,
            \T_LOGICAL_AND,
            \T_LOGICAL_OR,
            \T_LOGICAL_XOR,
            \T_COALESCE,
            \T_RETURN,
            \T_ECHO,
            \T_PRINT,
            \T_THROW,
        ], true);
    }

    private function normalizeLeftExpression(string $expression): string
    {
        $expression = \trim($expression);

        while ($this->isWrappedInParentheses($expression)) {
            $expression = \trim(\substr($expression, 1, -1));
        }

        return $expression;
    }

    private function isWrappedInParentheses(string $expression): bool
    {
        if (\strlen($expression) < 2 || $expression[0] !== '(' || \substr($expression, -1) !== ')') {
            return false;
        }

        $depth = 0;
        $length = \strlen($expression);

        for ($i = 0; $i < $length; $i++) {
            if ($expression[$i] === '(') {
                $depth++;
            } elseif ($expression[$i] === ')') {
                $depth--;
            }

            if ($depth === 0 && $i < $length - 1) {
                return false;
            }
        }

        return $depth === 0;
    }

    private function isIdentifierToken(array $entry): bool
    {
        if (!\is_array($entry['token'])) {
            return false;
        }

        return \in_array($entry['token'][0], [
            \T_STRING,
            \defined('T_NAME_QUALIFIED') ? \T_NAME_QUALIFIED : \T_STRING,
            \defined('T_NAME_FULLY_QUALIFIED') ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
            \defined('T_NAME_RELATIVE') ? \T_NAME_RELATIVE : \T_STRING,
        ], true);
    }

    private function isObjectAccessOperatorToken($token): bool
    {
        if (!\is_array($token)) {
            return false;
        }

        if ($token[0] === \T_OBJECT_OPERATOR) {
            return true;
        }

        return \defined('T_NULLSAFE_OBJECT_OPERATOR') && $token[0] === \T_NULLSAFE_OBJECT_OPERATOR;
    }
}
