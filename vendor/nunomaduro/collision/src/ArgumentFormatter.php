<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use Tests\Unit\ArgumentFormatterTest;

/**
 * @internal
 *
 * @see ArgumentFormatterTest
 */
final class ArgumentFormatter
{
    private const MAX_STRING_LENGTH = 1000;

    public function format(array $arguments, bool $recursive = true): string
    {
        $result = [];

        foreach ($arguments as $argument) {
            switch (true) {
                case is_string($argument):
                    $result[] = '"'.(mb_strlen($argument) > self::MAX_STRING_LENGTH ? mb_substr($argument, 0, self::MAX_STRING_LENGTH).'...' : $argument).'"';
                    break;
                case is_array($argument):
                    $associative = array_keys($argument) !== range(0, count($argument) - 1);
                    if ($recursive && $associative && count($argument) <= 5) {
                        $result[] = '['.$this->format($argument, false).']';
                    }
                    break;
                case is_object($argument):
                    $class = get_class($argument);
                    $result[] = "Object($class)";
                    break;
            }
        }

        return implode(', ', $result);
    }
}
