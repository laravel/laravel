<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Api;

use function assert;
use function class_exists;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\After;
use PHPUnit\Metadata\AfterClass;
use PHPUnit\Metadata\Before;
use PHPUnit\Metadata\BeforeClass;
use PHPUnit\Metadata\Parser\Registry;
use PHPUnit\Metadata\PostCondition;
use PHPUnit\Metadata\PreCondition;
use PHPUnit\Runner\HookMethod;
use PHPUnit\Runner\HookMethodCollection;
use PHPUnit\Util\Reflection;
use ReflectionClass;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class HookMethods
{
    /**
     * @var array<class-string, array{beforeClass: HookMethodCollection, before: HookMethodCollection, preCondition: HookMethodCollection, postCondition: HookMethodCollection, after: HookMethodCollection, afterClass: HookMethodCollection}>
     */
    private static array $hookMethods = [];

    /**
     * @param class-string<TestCase> $className
     *
     * @return array{beforeClass: HookMethodCollection, before: HookMethodCollection, preCondition: HookMethodCollection, postCondition: HookMethodCollection, after: HookMethodCollection, afterClass: HookMethodCollection}
     */
    public function hookMethods(string $className): array
    {
        if (!class_exists($className)) {
            return self::emptyHookMethodsArray();
        }

        if (isset(self::$hookMethods[$className])) {
            return self::$hookMethods[$className];
        }

        self::$hookMethods[$className] = self::emptyHookMethodsArray();

        foreach (Reflection::methodsDeclaredDirectlyInTestClass(new ReflectionClass($className)) as $method) {
            $methodName = $method->getName();

            assert(!empty($methodName));

            $metadata = Registry::parser()->forMethod($className, $methodName);

            if ($method->isStatic()) {
                if ($metadata->isBeforeClass()->isNotEmpty()) {
                    $beforeClass = $metadata->isBeforeClass()->asArray()[0];
                    assert($beforeClass instanceof BeforeClass);

                    self::$hookMethods[$className]['beforeClass']->add(
                        new HookMethod($methodName, $beforeClass->priority()),
                    );
                }

                if ($metadata->isAfterClass()->isNotEmpty()) {
                    $afterClass = $metadata->isAfterClass()->asArray()[0];
                    assert($afterClass instanceof AfterClass);

                    self::$hookMethods[$className]['afterClass']->add(
                        new HookMethod($methodName, $afterClass->priority()),
                    );
                }
            }

            if ($metadata->isBefore()->isNotEmpty()) {
                $before = $metadata->isBefore()->asArray()[0];
                assert($before instanceof Before);

                self::$hookMethods[$className]['before']->add(
                    new HookMethod($methodName, $before->priority()),
                );
            }

            if ($metadata->isPreCondition()->isNotEmpty()) {
                $preCondition = $metadata->isPreCondition()->asArray()[0];
                assert($preCondition instanceof PreCondition);

                self::$hookMethods[$className]['preCondition']->add(
                    new HookMethod($methodName, $preCondition->priority()),
                );
            }

            if ($metadata->isPostCondition()->isNotEmpty()) {
                $postCondition = $metadata->isPostCondition()->asArray()[0];
                assert($postCondition instanceof PostCondition);

                self::$hookMethods[$className]['postCondition']->add(
                    new HookMethod($methodName, $postCondition->priority()),
                );
            }

            if ($metadata->isAfter()->isNotEmpty()) {
                $after = $metadata->isAfter()->asArray()[0];
                assert($after instanceof After);

                self::$hookMethods[$className]['after']->add(
                    new HookMethod($methodName, $after->priority()),
                );
            }
        }

        return self::$hookMethods[$className];
    }

    /**
     * @return array{beforeClass: HookMethodCollection, before: HookMethodCollection, preCondition: HookMethodCollection, postCondition: HookMethodCollection, after: HookMethodCollection, afterClass: HookMethodCollection}
     */
    private function emptyHookMethodsArray(): array
    {
        return [
            'beforeClass'   => HookMethodCollection::defaultBeforeClass(),
            'before'        => HookMethodCollection::defaultBefore(),
            'preCondition'  => HookMethodCollection::defaultPreCondition(),
            'postCondition' => HookMethodCollection::defaultPostCondition(),
            'after'         => HookMethodCollection::defaultAfter(),
            'afterClass'    => HookMethodCollection::defaultAfterClass(),
        ];
    }
}
