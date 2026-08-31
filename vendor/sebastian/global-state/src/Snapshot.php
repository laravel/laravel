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

use function array_keys;
use function array_merge;
use function array_reverse;
use function assert;
use function get_declared_classes;
use function get_declared_interfaces;
use function get_declared_traits;
use function get_defined_constants;
use function get_defined_functions;
use function get_included_files;
use function in_array;
use function ini_get_all;
use function is_array;
use function is_object;
use function is_resource;
use function is_scalar;
use function serialize;
use function unserialize;
use ReflectionClass;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SebastianBergmann\RecursionContext\Context;
use Throwable;

/**
 * A snapshot of global state.
 */
final class Snapshot
{
    private ExcludeList $excludeList;

    /**
     * @var array<string, mixed>
     */
    private array $globalVariables = [];

    /**
     * @var list<string>
     */
    private array $superGlobalArrays = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $superGlobalVariables = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $staticProperties = [];

    /**
     * @var array<non-empty-string, array{global_value: string, local_value: string, access: int}>
     */
    private array $iniSettings = [];

    /**
     * @var list<string>
     */
    private array $includedFiles = [];

    /**
     * @var array<string, mixed>
     */
    private array $constants = [];

    /**
     * @var list<callable-string>
     */
    private array $functions = [];

    /**
     * @var list<class-string>
     */
    private array $interfaces = [];

    /**
     * @var list<class-string>
     */
    private array $classes = [];

    /**
     * @var list<class-string>
     */
    private array $traits = [];

    public function __construct(?ExcludeList $excludeList = null, bool $includeGlobalVariables = true, bool $includeStaticProperties = true, bool $includeConstants = true, bool $includeFunctions = true, bool $includeClasses = true, bool $includeInterfaces = true, bool $includeTraits = true, bool $includeIniSettings = true, bool $includeIncludedFiles = true)
    {
        $this->excludeList = $excludeList ?: new ExcludeList;

        if ($includeConstants) {
            $this->snapshotConstants();
        }

        if ($includeFunctions) {
            $this->snapshotFunctions();
        }

        if ($includeClasses || $includeStaticProperties) {
            $this->snapshotClasses();
        }

        if ($includeInterfaces) {
            $this->snapshotInterfaces();
        }

        if ($includeGlobalVariables) {
            $this->setupSuperGlobalArrays();
            $this->snapshotGlobals();
        }

        if ($includeStaticProperties) {
            $this->snapshotStaticProperties();
        }

        if ($includeIniSettings) {
            $iniSettings = ini_get_all(null, false);

            assert($iniSettings !== false);

            $this->iniSettings = $iniSettings;
        }

        if ($includeIncludedFiles) {
            $this->includedFiles = get_included_files();
        }

        if ($includeTraits) {
            $this->traits = get_declared_traits();
        }
    }

    public function excludeList(): ExcludeList
    {
        return $this->excludeList;
    }

    /**
     * @return array<string, mixed>
     */
    public function globalVariables(): array
    {
        return $this->globalVariables;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function superGlobalVariables(): array
    {
        return $this->superGlobalVariables;
    }

    /**
     * @return list<string>
     */
    public function superGlobalArrays(): array
    {
        return $this->superGlobalArrays;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function staticProperties(): array
    {
        return $this->staticProperties;
    }

    /**
     * @return array<non-empty-string, array{global_value: string, local_value: string, access: int}>
     */
    public function iniSettings(): array
    {
        return $this->iniSettings;
    }

    /**
     * @return list<string>
     */
    public function includedFiles(): array
    {
        return $this->includedFiles;
    }

    /**
     * @return array<string, mixed>
     */
    public function constants(): array
    {
        return $this->constants;
    }

    /**
     * @return list<callable-string>
     */
    public function functions(): array
    {
        return $this->functions;
    }

    /**
     * @return list<class-string>
     */
    public function interfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * @return list<class-string>
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * @return list<class-string>
     */
    public function traits(): array
    {
        return $this->traits;
    }

    private function snapshotConstants(): void
    {
        $constants = get_defined_constants(true);

        if (isset($constants['user'])) {
            $this->constants = $constants['user'];
        }
    }

    private function snapshotFunctions(): void
    {
        $functions = get_defined_functions();

        $this->functions = $functions['user'];
    }

    private function snapshotClasses(): void
    {
        foreach (array_reverse(get_declared_classes()) as $className) {
            $class = new ReflectionClass($className);

            if (!$class->isUserDefined()) {
                break;
            }

            $this->classes[] = $className;
        }

        $this->classes = array_reverse($this->classes);
    }

    private function snapshotInterfaces(): void
    {
        foreach (array_reverse(get_declared_interfaces()) as $interfaceName) {
            $class = new ReflectionClass($interfaceName);

            if (!$class->isUserDefined()) {
                break;
            }

            $this->interfaces[] = $interfaceName;
        }

        $this->interfaces = array_reverse($this->interfaces);
    }

    private function snapshotGlobals(): void
    {
        $superGlobalArrays = $this->superGlobalArrays();

        foreach ($superGlobalArrays as $superGlobalArray) {
            $this->snapshotSuperGlobalArray($superGlobalArray);
        }

        foreach (array_keys($GLOBALS) as $key) {
            if ($key !== 'GLOBALS' &&
                !in_array($key, $superGlobalArrays, true) &&
                $this->canBeSerialized($GLOBALS[$key]) &&
                !$this->excludeList->isGlobalVariableExcluded($key)) {
                /* @noinspection UnserializeExploitsInspection */
                $this->globalVariables[$key] = unserialize(serialize($GLOBALS[$key]));
            }
        }
    }

    private function snapshotSuperGlobalArray(string $superGlobalArray): void
    {
        $this->superGlobalVariables[$superGlobalArray] = [];

        if (isset($GLOBALS[$superGlobalArray]) && is_array($GLOBALS[$superGlobalArray])) {
            foreach ($GLOBALS[$superGlobalArray] as $key => $value) {
                /* @noinspection UnserializeExploitsInspection */
                $this->superGlobalVariables[$superGlobalArray][$key] = unserialize(serialize($value));
            }
        }
    }

    private function snapshotStaticProperties(): void
    {
        foreach ($this->classes as $className) {
            $class    = new ReflectionClass($className);
            $snapshot = [];

            foreach ($class->getProperties() as $property) {
                if ($property->isStatic()) {
                    $name = $property->getName();

                    if ($this->excludeList->isStaticPropertyExcluded($className, $name)) {
                        continue;
                    }

                    if (!$property->isInitialized()) {
                        continue;
                    }

                    $value = $property->getValue();

                    if ($this->canBeSerialized($value)) {
                        /* @noinspection UnserializeExploitsInspection */
                        $snapshot[$name] = unserialize(serialize($value));
                    }
                }
            }

            if (!empty($snapshot)) {
                $this->staticProperties[$className] = $snapshot;
            }
        }
    }

    private function setupSuperGlobalArrays(): void
    {
        $this->superGlobalArrays = [
            '_ENV',
            '_POST',
            '_GET',
            '_COOKIE',
            '_SERVER',
            '_FILES',
            '_REQUEST',
        ];
    }

    private function canBeSerialized(mixed $variable): bool
    {
        if (is_scalar($variable) || $variable === null) {
            return true;
        }

        if (is_resource($variable)) {
            return false;
        }

        foreach ($this->enumerateObjectsAndResources($variable) as $value) {
            if (is_resource($value)) {
                return false;
            }

            if (is_object($value)) {
                $class = new ReflectionClass($value);

                if ($class->isAnonymous()) {
                    return false;
                }

                try {
                    @serialize($value);
                } catch (Throwable $t) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array<mixed>
     */
    private function enumerateObjectsAndResources(mixed $variable, Context $processed = new Context): array
    {
        $result = [];

        if ($processed->contains($variable)) {
            return $result;
        }

        $array = $variable;

        /* @noinspection UnusedFunctionResultInspection */
        $processed->add($variable);

        if (is_array($variable)) {
            /** @phpstan-ignore foreach.nonIterable */
            foreach ($array as $element) {
                if (!is_array($element) && !is_object($element) && !is_resource($element)) {
                    continue;
                }

                if (!is_resource($element)) {
                    /** @noinspection SlowArrayOperationsInLoopInspection */
                    $result = array_merge(
                        $result,
                        $this->enumerateObjectsAndResources($element, $processed),
                    );
                } else {
                    $result[] = $element;
                }
            }
        } else {
            $result[] = $variable;

            foreach ((new ObjectReflector)->getProperties($variable) as $value) {
                if (!is_array($value) && !is_object($value) && !is_resource($value)) {
                    continue;
                }

                if (!is_resource($value)) {
                    /** @noinspection SlowArrayOperationsInLoopInspection */
                    $result = array_merge(
                        $result,
                        $this->enumerateObjectsAndResources($value, $processed),
                    );
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}
