<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use const DEBUG_BACKTRACE_IGNORE_ARGS;
use const DEBUG_BACKTRACE_PROVIDE_OBJECT;
use function debug_backtrace;
use function str_starts_with;
use PHPUnit\Event\Code\NoTestCaseObjectOnCallStackException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Parser\Registry;
use ReflectionMethod;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Test
{
    /**
     * @throws NoTestCaseObjectOnCallStackException
     */
    public static function currentTestCase(): TestCase
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            if (isset($frame['object']) && $frame['object'] instanceof TestCase) {
                return $frame['object'];
            }
        }

        throw new NoTestCaseObjectOnCallStackException;
    }

    public static function isTestMethod(ReflectionMethod $method): bool
    {
        if (!$method->isPublic()) {
            return false;
        }

        if (str_starts_with($method->getName(), 'test')) {
            return true;
        }

        $metadata = Registry::parser()->forMethod(
            $method->getDeclaringClass()->getName(),
            $method->getName(),
        );

        return $metadata->isTest()->isNotEmpty();
    }
}
