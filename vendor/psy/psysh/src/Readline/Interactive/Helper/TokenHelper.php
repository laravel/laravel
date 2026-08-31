<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Helper;

/**
 * Shared token analysis utilities.
 */
class TokenHelper
{
    /** @var int[] Token types that indicate a statement continues on the next line */
    private const TRAILING_TOKEN_OPS = [
        \T_OBJECT_OPERATOR, \T_PAAMAYIM_NEKUDOTAYIM,
        \T_BOOLEAN_AND, \T_BOOLEAN_OR, \T_LOGICAL_AND, \T_LOGICAL_OR,
        \T_DOUBLE_ARROW, \T_COALESCE, \T_SPACESHIP,
    ];

    /** @var string[] Single-character operators that indicate continuation */
    private const TRAILING_CHAR_OPS = ['+', '-', '*', '/', '%', '.', '=', '&', '|', '^', '<', '>', ','];

    /**
     * Check whether the last non-whitespace token is a trailing operator.
     *
     * Used to determine whether a statement continues onto the next line.
     *
     * @param array $tokens token_get_all tokens
     */
    public static function hasTrailingOperator(array $tokens): bool
    {
        if (empty($tokens)) {
            return false;
        }

        $lastToken = null;
        for ($i = \count($tokens) - 1; $i >= 0; $i--) {
            $token = $tokens[$i];
            if (\is_array($token) && $token[0] === \T_WHITESPACE) {
                continue;
            }

            $lastToken = $token;
            break;
        }

        if ($lastToken === null) {
            return false;
        }

        if (\is_array($lastToken)) {
            return \in_array($lastToken[0], self::TRAILING_TOKEN_OPS, true);
        }

        return \in_array($lastToken, self::TRAILING_CHAR_OPS, true);
    }
}
