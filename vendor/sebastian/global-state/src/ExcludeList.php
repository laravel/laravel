<?php declare(strict_types=1);
/*
 * This file is part of sebastian/global-state.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\GlobalState;

use function in_array;
use function str_starts_with;
use ReflectionClass;

final class ExcludeList
{
    /**
     * @var array<non-empty-string, true>
     */
    private array $globalVariables = [];

    /**
     * @var list<non-empty-string>
     */
    private array $classes = [];

    /**
     * @var list<non-empty-string>
     */
    private array $classNamePrefixes = [];

    /**
     * @var list<non-empty-string>
     */
    private array $parentClasses = [];

    /**
     * @var list<non-empty-string>
     */
    private array $interfaces = [];

    /**
     * @var array<string, array<non-empty-string, true>>
     */
    private array $staticProperties = [];

    /**
     * @param non-empty-string $variableName
     */
    public function addGlobalVariable(string $variableName): void
    {
        $this->globalVariables[$variableName] = true;
    }

    /**
     * @param non-empty-string $className
     */
    public function addClass(string $className): void
    {
        $this->classes[] = $className;
    }

    /**
     * @param non-empty-string $className
     */
    public function addSubclassesOf(string $className): void
    {
        $this->parentClasses[] = $className;
    }

    /**
     * @param non-empty-string $interfaceName
     */
    public function addImplementorsOf(string $interfaceName): void
    {
        $this->interfaces[] = $interfaceName;
    }

    /**
     * @param non-empty-string $classNamePrefix
     */
    public function addClassNamePrefix(string $classNamePrefix): void
    {
        $this->classNamePrefixes[] = $classNamePrefix;
    }

    /**
     * @param non-empty-string $className
     * @param non-empty-string $propertyName
     */
    public function addStaticProperty(string $className, string $propertyName): void
    {
        if (!isset($this->staticProperties[$className])) {
            $this->staticProperties[$className] = [];
        }

        $this->staticProperties[$className][$propertyName] = true;
    }

    public function isGlobalVariableExcluded(string $variableName): bool
    {
        return isset($this->globalVariables[$variableName]);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $propertyName
     */
    public function isStaticPropertyExcluded(string $className, string $propertyName): bool
    {
        if (in_array($className, $this->classes, true)) {
            return true;
        }

        foreach ($this->classNamePrefixes as $prefix) {
            if (str_starts_with($className, $prefix)) {
                return true;
            }
        }

        $class = new ReflectionClass($className);

        foreach ($this->parentClasses as $type) {
            if ($class->isSubclassOf($type)) {
                return true;
            }
        }

        foreach ($this->interfaces as $type) {
            if ($class->implementsInterface($type)) {
                return true;
            }
        }

        return isset($this->staticProperties[$className][$propertyName]);
    }
}
