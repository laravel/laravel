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
 * A MongoDB Client tab completion Matcher.
 *
 * This matcher provides completion for MongoClient database names.
 *
 * @author Marc Garcia <markcial@gmail.com>
 */
class MongoClientMatcher extends AbstractContextAwareMatcher
{
    /**
     * {@inheritdoc}
     */
    public function getMatches(array $tokens, array $info = []): array
    {
        $input = $this->getInput($tokens);

        $firstToken = \array_pop($tokens);
        if (self::tokenIs($firstToken, self::T_STRING)) {
            // second token is the object operator
            \array_pop($tokens);
        }
        $objectToken = \array_pop($tokens);
        $objectName = \str_replace('$', '', $objectToken[1]);
        $object = $this->getVariable($objectName);

        if (!$object instanceof \MongoClient) {
            return [];
        }

        $list = $object->listDBs();

        return \array_filter(
            \array_map(fn ($info) => $info['name'], $list['databases']),
            fn ($var) => AbstractMatcher::startsWith($input, $var)
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
            case self::tokenIs($token, self::T_OBJECT_OPERATOR):
            case self::tokenIs($prevToken, self::T_OBJECT_OPERATOR):
                return true;
        }

        return false;
    }
}
