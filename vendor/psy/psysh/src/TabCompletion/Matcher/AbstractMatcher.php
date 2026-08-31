<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\TabCompletion\Matcher;

/**
 * Abstract tab completion Matcher.
 *
 * @author Marc Garcia <markcial@gmail.com>
 */
abstract class AbstractMatcher
{
    /** Syntax types */
    const CONSTANT_SYNTAX = '^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$';
    const VAR_SYNTAX = '^\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$';
    const MISC_OPERATORS = '+-*/^|&';
    /** Token values */
    const T_OPEN_TAG = 'T_OPEN_TAG';
    const T_VARIABLE = 'T_VARIABLE';
    const T_OBJECT_OPERATOR = 'T_OBJECT_OPERATOR';
    const T_DOUBLE_COLON = 'T_DOUBLE_COLON';
    const T_NEW = 'T_NEW';
    const T_CLONE = 'T_CLONE';
    const T_NS_SEPARATOR = 'T_NS_SEPARATOR';
    const T_STRING = 'T_STRING';
    const T_NAME_QUALIFIED = 'T_NAME_QUALIFIED';
    const T_WHITESPACE = 'T_WHITESPACE';
    const T_AND_EQUAL = 'T_AND_EQUAL';
    const T_BOOLEAN_AND = 'T_BOOLEAN_AND';
    const T_BOOLEAN_OR = 'T_BOOLEAN_OR';

    const T_ENCAPSED_AND_WHITESPACE = 'T_ENCAPSED_AND_WHITESPACE';
    const T_REQUIRE = 'T_REQUIRE';
    const T_REQUIRE_ONCE = 'T_REQUIRE_ONCE';
    const T_INCLUDE = 'T_INCLUDE';
    const T_INCLUDE_ONCE = 'T_INCLUDE_ONCE';

    /**
     * Check whether this matcher can provide completions for $tokens.
     *
     * @param array $tokens Tokenized readline input
     *
     * @return bool
     */
    public function hasMatched(array $tokens): bool
    {
        return false;
    }

    /**
     * Get current readline input word.
     *
     * @param array $tokens Tokenized readline input (see token_get_all)
     */
    protected function getInput(array $tokens): string
    {
        $var = '';
        $firstToken = \array_pop($tokens);
        if (self::tokenIs($firstToken, self::T_STRING)) {
            $var = $firstToken[1];
        }

        return $var;
    }

    /**
     * Get current namespace and class (if any) from readline input.
     *
     * @param array $tokens Tokenized readline input (see token_get_all)
     */
    protected function getNamespaceAndClass(array $tokens): string
    {
        $class = '';
        while (self::hasToken(
            [self::T_NS_SEPARATOR, self::T_STRING, self::T_NAME_QUALIFIED],
            $token = \array_pop($tokens)
        )) {
            if (self::needCompleteClass($token)) {
                continue;
            }

            $class = $token[1].$class;
        }

        return $class;
    }

    /**
     * Provide tab completion matches for readline input.
     *
     * @param array $tokens information substracted with get_token_all
     * @param array $info   readline_info object
     *
     * @return array The matches resulting from the query
     */
    abstract public function getMatches(array $tokens, array $info = []): array;

    /**
     * Check whether $word starts with $prefix.
     *
     * @param string $prefix
     * @param string $word
     */
    public static function startsWith(string $prefix, string $word): bool
    {
        return \preg_match(\sprintf('#^%s#', $prefix), $word);
    }

    /**
     * Check whether $token matches a given syntax pattern.
     *
     * @param mixed  $token  A PHP token (see token_get_all)
     * @param string $syntax A syntax pattern (default: variable pattern)
     */
    public static function hasSyntax($token, string $syntax = self::VAR_SYNTAX): bool
    {
        if (!\is_array($token)) {
            return false;
        }

        $regexp = \sprintf('#%s#', $syntax);

        return (bool) \preg_match($regexp, $token[1]);
    }

    /**
     * Check whether $token type is $which.
     *
     * @param mixed  $token A PHP token (see token_get_all)
     * @param string $which A PHP token type
     */
    public static function tokenIs($token, string $which): bool
    {
        if (!\is_array($token)) {
            return false;
        }

        return \token_name($token[0]) === $which;
    }

    /**
     * Check whether $token is an operator.
     *
     * @param mixed $token A PHP token (see token_get_all)
     */
    public static function isOperator($token): bool
    {
        if (!\is_string($token)) {
            return false;
        }

        // @phan-suppress-next-line PhanParamSuspiciousOrder - intentionally searching for token in constant string
        return \strpos(self::MISC_OPERATORS, $token) !== false;
    }

    public static function needCompleteClass($token): bool
    {
        return \in_array($token[1], ['doc', 'ls', 'show']);
    }

    /**
     * Check whether $token type is present in $coll.
     *
     * @param array $coll  A list of token types
     * @param mixed $token A PHP token (see token_get_all)
     */
    public static function hasToken(array $coll, $token): bool
    {
        if (!\is_array($token)) {
            return false;
        }

        return \in_array(\token_name($token[0]), $coll);
    }
}
