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

class FunctionDefaultParametersMatcher extends AbstractDefaultParametersMatcher
{
    public function getMatches(array $tokens, array $info = []): array
    {
        \array_pop($tokens); // open bracket

        $functionName = \array_pop($tokens);

        try {
            $reflection = new \ReflectionFunction($functionName[1]);
        } catch (\ReflectionException $e) {
            return [];
        }

        $parameters = $reflection->getParameters();

        return $this->getDefaultParameterCompletion($parameters);
    }

    public function hasMatched(array $tokens): bool
    {
        $openBracket = \array_pop($tokens);

        if ($openBracket !== '(') {
            return false;
        }

        $functionName = \array_pop($tokens);

        if (!self::tokenIs($functionName, self::T_STRING)) {
            return false;
        }

        if (!\function_exists($functionName[1])) {
            return false;
        }

        return true;
    }
}
