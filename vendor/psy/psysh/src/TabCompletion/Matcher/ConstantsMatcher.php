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
 * A constant name tab completion Matcher.
 *
 * This matcher provides completion for all defined constants.
 *
 * @author Marc Garcia <markcial@gmail.com>
 */
class ConstantsMatcher extends AbstractMatcher
{
    /**
     * {@inheritdoc}
     */
    public function getMatches(array $tokens, array $info = []): array
    {
        $const = $this->getInput($tokens);

        return \array_filter(\array_keys(\get_defined_constants()), fn ($constant) => AbstractMatcher::startsWith($const, $constant));
    }

    /**
     * {@inheritdoc}
     */
    public function hasMatched(array $tokens): bool
    {
        $token = \array_pop($tokens);
        $prevToken = \array_pop($tokens);

        switch (true) {
            case self::tokenIs($prevToken, self::T_NEW):
            case self::tokenIs($prevToken, self::T_NS_SEPARATOR):
                return false;
            case self::hasToken([self::T_OPEN_TAG, self::T_STRING], $token):
            case self::isOperator($token):
                return true;
        }

        return false;
    }
}
