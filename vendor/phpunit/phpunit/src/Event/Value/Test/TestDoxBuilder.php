<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Code;

use PHPUnit\Framework\TestCase;
use PHPUnit\Logging\TestDox\NamePrettifier;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestDoxBuilder
{
    private static ?NamePrettifier $namePrettifier = null;

    public static function fromTestCase(TestCase $testCase): TestDox
    {
        $prettifier = self::namePrettifier();

        return new TestDox(
            $prettifier->prettifyTestClassName($testCase::class),
            $prettifier->prettifyTestCase($testCase, false),
            $prettifier->prettifyTestCase($testCase, true),
        );
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public static function fromClassNameAndMethodName(string $className, string $methodName): TestDox
    {
        $prettifier = self::namePrettifier();

        $prettifiedMethodName = $prettifier->prettifyTestMethodName($methodName);

        return new TestDox(
            $prettifier->prettifyTestClassName($className),
            $prettifiedMethodName,
            $prettifiedMethodName,
        );
    }

    private static function namePrettifier(): NamePrettifier
    {
        if (self::$namePrettifier === null) {
            self::$namePrettifier = new NamePrettifier;
        }

        return self::$namePrettifier;
    }
}
