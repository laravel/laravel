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
 * A variable name tab completion Matcher.
 *
 * This matcher provides completion for variable names in the current Context.
 *
 * @author Marc Garcia <markcial@gmail.com>
 */
class VariablesMatcher extends AbstractContextAwareMatcher
{
    /**
     * {@inheritdoc}
     */
    public function getMatches(array $tokens, array $info = []): array
    {
        $var = \str_replace('$', '', $this->getInput($tokens));

        return \array_filter(
            \array_keys($this->getVariables()),
            fn ($variable) => AbstractMatcher::startsWith($var, $variable)
        );
    }

    /**
     * Get current readline input word (variable name).
     *
     * Overrides parent to handle T_VARIABLE tokens.
     *
     * @param array $tokens Tokenized readline input (see token_get_all)
     */
    protected function getInput(array $tokens): string
    {
        $var = '';
        $firstToken = \array_pop($tokens);

        // Handle T_VARIABLE tokens (e.g., $varName)
        if (\is_array($firstToken) && self::tokenIs($firstToken, self::T_VARIABLE)) {
            // Token value includes the $, so strip it
            $var = \ltrim((string) $firstToken[1], '$');
        } elseif (\is_array($firstToken) && self::tokenIs($firstToken, self::T_STRING)) {
            // Fallback to parent behavior for T_STRING tokens
            $var = (string) $firstToken[1];
        }

        return $var;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMatched(array $tokens): bool
    {
        $token = \array_pop($tokens);

        switch (true) {
            case self::hasToken([self::T_OPEN_TAG, self::T_VARIABLE], $token):
            case \is_string($token) && $token === '$':
            case self::isOperator($token):
                return true;
        }

        return false;
    }
}
