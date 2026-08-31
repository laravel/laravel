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
 * A class attribute tab completion Matcher.
 *
 * Given a namespace and class, this matcher provides completion for constants
 * and static properties.
 *
 * @author Marc Garcia <markcial@gmail.com>
 */
class ClassAttributesMatcher extends AbstractMatcher
{
    /**
     * {@inheritdoc}
     */
    public function getMatches(array $tokens, array $info = []): array
    {
        $input = $this->getInput($tokens);

        $firstToken = \array_pop($tokens);
        if (self::tokenIs($firstToken, self::T_STRING)) {
            // second token is the nekudotayim operator
            \array_pop($tokens);
        }

        $class = $this->getNamespaceAndClass($tokens);

        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $re) {
            return [];
        }

        $vars = \array_merge(
            \array_map(
                function ($var) {
                    return '$'.$var;
                },
                \array_keys($reflection->getStaticProperties())
            ),
            \array_keys($reflection->getConstants())
        );

        return \array_map(
            function ($name) use ($class) {
                $chunks = \explode('\\', $class);
                $className = \array_pop($chunks);

                return $className.'::'.$name;
            },
            \array_filter(
                $vars,
                function ($var) use ($input) {
                    return AbstractMatcher::startsWith($input, $var);
                }
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hasMatched(array $tokens): bool
    {
        $token = \array_pop($tokens);
        $prevToken = \array_pop($tokens);

        switch (true) {
            case self::tokenIs($prevToken, self::T_DOUBLE_COLON) && self::tokenIs($token, self::T_STRING):
            case self::tokenIs($token, self::T_DOUBLE_COLON):
                return true;
        }

        return false;
    }
}
