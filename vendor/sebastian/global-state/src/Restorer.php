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

use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function in_array;
use function is_array;
use ReflectionClass;
use ReflectionProperty;

final class Restorer
{
    public function restoreGlobalVariables(Snapshot $snapshot): void
    {
        $superGlobalArrays = $snapshot->superGlobalArrays();

        foreach ($superGlobalArrays as $superGlobalArray) {
            $this->restoreSuperGlobalArray($snapshot, $superGlobalArray);
        }

        $globalVariables = $snapshot->globalVariables();

        foreach (array_keys($GLOBALS) as $key) {
            if ($key !== 'GLOBALS' &&
                !in_array($key, $superGlobalArrays, true) &&
                !$snapshot->excludeList()->isGlobalVariableExcluded((string) $key)) {
                if (array_key_exists($key, $globalVariables)) {
                    $GLOBALS[$key] = $globalVariables[$key];
                } else {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }

    public function restoreStaticProperties(Snapshot $snapshot): void
    {
        $current    = new Snapshot($snapshot->excludeList(), false, false, false, false, true, false, false, false, false);
        $newClasses = array_diff($current->classes(), $snapshot->classes());

        unset($current);

        foreach ($snapshot->staticProperties() as $className => $staticProperties) {
            foreach ($staticProperties as $name => $value) {
                $reflector = new ReflectionProperty($className, $name);
                $reflector->setValue(null, $value);
            }
        }

        foreach ($newClasses as $className) {
            $class    = new ReflectionClass($className);
            $defaults = $class->getDefaultProperties();

            foreach ($class->getProperties() as $property) {
                if (!$property->isStatic()) {
                    continue;
                }

                $name = $property->getName();

                if ($snapshot->excludeList()->isStaticPropertyExcluded($className, $name)) {
                    continue;
                }

                if (!isset($defaults[$name])) {
                    continue;
                }

                $property->setValue(null, $defaults[$name]);
            }
        }
    }

    private function restoreSuperGlobalArray(Snapshot $snapshot, string $superGlobalArray): void
    {
        $superGlobalVariables = $snapshot->superGlobalVariables();

        if (isset($GLOBALS[$superGlobalArray], $superGlobalVariables[$superGlobalArray]) &&
            is_array($GLOBALS[$superGlobalArray])) {
            $keys = array_keys(
                array_merge(
                    $GLOBALS[$superGlobalArray],
                    $superGlobalVariables[$superGlobalArray],
                ),
            );

            foreach ($keys as $key) {
                if (isset($superGlobalVariables[$superGlobalArray][$key])) {
                    $GLOBALS[$superGlobalArray][$key] = $superGlobalVariables[$superGlobalArray][$key];
                } else {
                    unset($GLOBALS[$superGlobalArray][$key]);
                }
            }
        }
    }
}
