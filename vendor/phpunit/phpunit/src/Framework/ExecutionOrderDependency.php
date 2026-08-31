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

use function array_filter;
use function array_map;
use function array_values;
use function explode;
use function in_array;
use function str_contains;
use PHPUnit\Metadata\DependsOnClass;
use PHPUnit\Metadata\DependsOnMethod;
use Stringable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ExecutionOrderDependency implements Stringable
{
    private string $className  = '';
    private string $methodName = '';
    private readonly bool $shallowClone;
    private readonly bool $deepClone;

    public static function invalid(): self
    {
        return new self(
            '',
            '',
            false,
            false,
        );
    }

    public static function forClass(DependsOnClass $metadata): self
    {
        return new self(
            $metadata->className(),
            'class',
            $metadata->deepClone(),
            $metadata->shallowClone(),
        );
    }

    public static function forMethod(DependsOnMethod $metadata): self
    {
        return new self(
            $metadata->className(),
            $metadata->methodName(),
            $metadata->deepClone(),
            $metadata->shallowClone(),
        );
    }

    /**
     * @param list<ExecutionOrderDependency> $dependencies
     *
     * @return list<ExecutionOrderDependency>
     */
    public static function filterInvalid(array $dependencies): array
    {
        return array_values(
            array_filter(
                $dependencies,
                static fn (self $d) => $d->isValid(),
            ),
        );
    }

    /**
     * @param list<ExecutionOrderDependency> $existing
     * @param list<ExecutionOrderDependency> $additional
     *
     * @return list<ExecutionOrderDependency>
     */
    public static function mergeUnique(array $existing, array $additional): array
    {
        $existingTargets = array_map(
            static fn ($dependency) => $dependency->getTarget(),
            $existing,
        );

        foreach ($additional as $dependency) {
            $additionalTarget = $dependency->getTarget();

            if (in_array($additionalTarget, $existingTargets, true)) {
                continue;
            }

            $existingTargets[] = $additionalTarget;
            $existing[]        = $dependency;
        }

        return $existing;
    }

    /**
     * @param list<ExecutionOrderDependency> $left
     * @param list<ExecutionOrderDependency> $right
     *
     * @return list<ExecutionOrderDependency>
     */
    public static function diff(array $left, array $right): array
    {
        if ($right === []) {
            return $left;
        }

        if ($left === []) {
            return [];
        }

        $diff         = [];
        $rightTargets = array_map(
            static fn ($dependency) => $dependency->getTarget(),
            $right,
        );

        foreach ($left as $dependency) {
            if (in_array($dependency->getTarget(), $rightTargets, true)) {
                continue;
            }

            $diff[] = $dependency;
        }

        return $diff;
    }

    public function __construct(string $classOrCallableName, ?string $methodName = null, bool $deepClone = false, bool $shallowClone = false)
    {
        $this->deepClone    = $deepClone;
        $this->shallowClone = $shallowClone;

        if ($classOrCallableName === '') {
            return;
        }

        if (str_contains($classOrCallableName, '::')) {
            [$this->className, $this->methodName] = explode('::', $classOrCallableName);
        } else {
            $this->className  = $classOrCallableName;
            $this->methodName = !empty($methodName) ? $methodName : 'class';
        }
    }

    public function __toString(): string
    {
        return $this->getTarget();
    }

    public function isValid(): bool
    {
        // Invalid dependencies can be declared and are skipped by the runner
        return $this->className !== '' && $this->methodName !== '';
    }

    public function shallowClone(): bool
    {
        return $this->shallowClone;
    }

    public function deepClone(): bool
    {
        return $this->deepClone;
    }

    public function targetIsClass(): bool
    {
        return $this->methodName === 'class';
    }

    public function getTarget(): string
    {
        return $this->isValid()
            ? $this->className . '::' . $this->methodName
            : '';
    }

    public function getTargetClassName(): string
    {
        return $this->className;
    }
}
