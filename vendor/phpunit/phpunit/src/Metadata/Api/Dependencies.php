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
use PHPUnit\Framework\ExecutionOrderDependency;
use PHPUnit\Metadata\DependsOnClass;
use PHPUnit\Metadata\DependsOnMethod;
use PHPUnit\Metadata\Parser\Registry;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Dependencies
{
    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     *
     * @return list<ExecutionOrderDependency>
     */
    public static function dependencies(string $className, string $methodName): array
    {
        $dependencies = [];

        foreach (Registry::parser()->forClassAndMethod($className, $methodName)->isDepends() as $metadata) {
            if ($metadata->isDependsOnClass()) {
                assert($metadata instanceof DependsOnClass);

                $dependencies[] = ExecutionOrderDependency::forClass($metadata);

                continue;
            }

            assert($metadata instanceof DependsOnMethod);

            if (empty($metadata->methodName())) {
                $dependencies[] = ExecutionOrderDependency::invalid();

                continue;
            }

            $dependencies[] = ExecutionOrderDependency::forMethod($metadata);
        }

        return $dependencies;
    }
}
