<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Suggestion\Source;

use Psy\Readline\Interactive\Suggestion\SuggestionResult;

/**
 * Provides function/method parameter signature suggestions.
 *
 * When cursor is inside empty parentheses after a function name,
 * suggests the parameter signature.
 */
class CallSignatureSource implements SourceInterface
{
    /** @var array<string, ?string> Cached formatted signatures keyed by function name. */
    private array $signatureCache = [];

    /**
     * {@inheritdoc}
     */
    public function getSuggestion(string $buffer, int $cursorPosition): ?SuggestionResult
    {
        if (\preg_match('/(?:->|::)\s*[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*\(\s*$/u', $buffer)) {
            return null;
        }

        if (!\preg_match('/([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)\(\s*$/u', $buffer, $matches)) {
            return null;
        }

        $functionName = $matches[1];

        $signature = $this->getFunctionSignature($functionName);

        if ($signature === null) {
            return null;
        }

        return SuggestionResult::forAppend(
            $signature,
            SuggestionResult::SOURCE_CALL_SIGNATURE,
            $cursorPosition,
            $signature.')'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 150; // Highest priority - very specific context
    }

    /**
     * Get function signature for display.
     *
     * Returns parameter list like: $array1, $array2, ...$arrays
     */
    private function getFunctionSignature(string $functionName): ?string
    {
        if (\array_key_exists($functionName, $this->signatureCache)) {
            return $this->signatureCache[$functionName];
        }

        $signature = null;
        try {
            if (\function_exists($functionName)) {
                $reflection = new \ReflectionFunction($functionName);
                $signature = $this->formatParameters($reflection->getParameters());
            }
        } catch (\ReflectionException $e) {
            // Leave as null.
        }

        $this->signatureCache[$functionName] = $signature;

        return $signature;
    }

    /**
     * Format reflection parameters into readable signature.
     *
     * @param \ReflectionParameter[] $parameters
     */
    private function formatParameters(array $parameters): string
    {
        if (empty($parameters)) {
            return '';
        }

        $parts = [];

        foreach ($parameters as $param) {
            $paramStr = '';

            if ($param->isVariadic()) {
                $paramStr .= '...';
            }

            $paramStr .= '$'.$param->getName();

            if ($param->isOptional() && !$param->isVariadic()) {
                $paramStr .= ' = '.$this->formatDefaultValue($param);
            }

            $parts[] = $paramStr;
        }

        return \implode(', ', $parts);
    }

    /**
     * Format a parameter's default value for display in the signature.
     */
    private function formatDefaultValue(\ReflectionParameter $parameter): string
    {
        try {
            $default = $parameter->getDefaultValue();
        } catch (\ReflectionException $e) {
            return '...';
        }

        if ($default === null) {
            return 'null';
        }

        if (\is_bool($default)) {
            return $default ? 'true' : 'false';
        }

        if (\is_string($default)) {
            if (\strlen($default) > 20) {
                return '"'.\substr($default, 0, 17).'..."';
            }

            return '"'.$default.'"';
        }

        if (\is_array($default)) {
            return '[]';
        }

        return \var_export($default, true);
    }
}
