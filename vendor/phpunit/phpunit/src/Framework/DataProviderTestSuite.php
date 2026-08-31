<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use function assert;
use function class_exists;
use function explode;
use PHPUnit\Framework\TestSize\TestSize;
use PHPUnit\Metadata\Api\Groups;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class DataProviderTestSuite extends TestSuite
{
    /**
     * @var list<ExecutionOrderDependency>
     */
    private array $dependencies = [];

    /**
     * @var ?non-empty-list<ExecutionOrderDependency>
     */
    private ?array $providedTests = null;

    /**
     * @param list<ExecutionOrderDependency> $dependencies
     */
    public function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;

        foreach ($this->tests() as $test) {
            if (!$test instanceof TestCase) {
                continue;
            }

            $test->setDependencies($dependencies);
        }
    }

    /**
     * @return non-empty-list<ExecutionOrderDependency>
     */
    public function provides(): array
    {
        if ($this->providedTests === null) {
            $this->providedTests = [new ExecutionOrderDependency($this->name())];
        }

        return $this->providedTests;
    }

    /**
     * @return list<ExecutionOrderDependency>
     */
    public function requires(): array
    {
        // A DataProviderTestSuite does not have to traverse its child tests
        // as these are inherited and cannot reference dataProvider rows directly
        return $this->dependencies;
    }

    /**
     * Returns the size of each test created using the data provider(s).
     */
    public function size(): TestSize
    {
        [$className, $methodName] = explode('::', $this->name());

        assert(class_exists($className));
        assert($methodName !== '');

        return (new Groups)->size($className, $methodName);
    }
}
