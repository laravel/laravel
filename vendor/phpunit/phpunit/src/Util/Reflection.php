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

use function array_keys;
use function array_merge;
use function array_reverse;
use function assert;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Reflection
{
    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     *
     * @return array{file: non-empty-string, line: non-negative-int}
     */
    public static function sourceLocationFor(string $className, string $methodName): array
    {
        try {
            $reflector = new ReflectionMethod($className, $methodName);

            $file = $reflector->getFileName();
            $line = $reflector->getStartLine();
        } catch (ReflectionException) {
            $file = 'unknown';
            $line = 0;
        }

        assert($file !== false && $file !== '');
        assert($line !== false && $line >= 0);

        return [
            'file' => $file,
            'line' => $line,
        ];
    }

    /**
     * @param ReflectionClass<TestCase> $class
     *
     * @return list<ReflectionMethod>
     */
    public static function publicMethodsDeclaredDirectlyInTestClass(ReflectionClass $class): array
    {
        return self::filterAndSortMethods($class, ReflectionMethod::IS_PUBLIC, true);
    }

    /**
     * @param ReflectionClass<TestCase> $class
     *
     * @return list<ReflectionMethod>
     */
    public static function methodsDeclaredDirectlyInTestClass(ReflectionClass $class): array
    {
        return self::filterAndSortMethods($class, null, false);
    }

    /**
     * @param ReflectionClass<TestCase> $class
     *
     * @return list<ReflectionMethod>
     */
    private static function filterAndSortMethods(ReflectionClass $class, ?int $filter, bool $sortHighestToLowest): array
    {
        $methodsByClass = [];

        foreach ($class->getMethods($filter) as $method) {
            $declaringClassName = $method->getDeclaringClass()->getName();

            if ($declaringClassName === TestCase::class) {
                continue;
            }

            if ($declaringClassName === Assert::class) {
                continue;
            }

            if (!isset($methodsByClass[$declaringClassName])) {
                $methodsByClass[$declaringClassName] = [];
            }

            $methodsByClass[$declaringClassName][] = $method;
        }

        $classNames = array_keys($methodsByClass);

        if ($sortHighestToLowest) {
            $classNames = array_reverse($classNames);
        }

        $methods = [];

        foreach ($classNames as $className) {
            $methods = array_merge($methods, $methodsByClass[$className]);
        }

        return $methods;
    }
}
