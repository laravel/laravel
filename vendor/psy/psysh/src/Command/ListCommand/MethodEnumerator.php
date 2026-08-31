<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command\ListCommand;

use Psy\Reflection\ReflectionMagicMethod;
use Psy\Util\Docblock;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Method Enumerator class.
 */
class MethodEnumerator extends Enumerator
{
    /**
     * {@inheritdoc}
     */
    protected function listItems(InputInterface $input, ?\Reflector $reflector = null, $target = null): array
    {
        // only list methods when a Reflector is present.
        if ($reflector === null) {
            return [];
        }

        // We can only list methods on actual class (or object) reflectors.
        if (!$reflector instanceof \ReflectionClass) {
            return [];
        }

        // only list methods if we are specifically asked
        if (!$input->getOption('methods')) {
            return [];
        }

        $showAll = $input->getOption('all');
        $noInherit = $input->getOption('no-inherit');
        $methods = $this->prepareMethods($this->getMethods($showAll, $reflector, $noInherit));

        if (empty($methods)) {
            return [];
        }

        $ret = [];
        $ret[$this->getKindLabel($reflector)] = $methods;

        return $ret;
    }

    /**
     * Get defined methods for the given class or object Reflector.
     *
     * @param bool             $showAll   Include private and protected methods
     * @param \ReflectionClass $reflector
     * @param bool             $noInherit Exclude inherited methods
     *
     * @return \ReflectionMethod[]
     */
    protected function getMethods(bool $showAll, \ReflectionClass $reflector, bool $noInherit = false): array
    {
        $className = $reflector->getName();

        $methods = [];
        foreach ($reflector->getMethods() as $name => $method) {
            // For some reason PHP reflection shows private methods from the parent class, even
            // though they're effectively worthless. Let's suppress them here, like --no-inherit
            if (($noInherit || $method->isPrivate()) && $method->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            if ($showAll || $method->isPublic()) {
                $methods[$method->getName()] = $method;
            }
        }

        // Add magic methods from docblock @method tags
        foreach (Docblock::getMagicMethods($reflector) as $method) {
            if ($noInherit && $method->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            // Skip if a real method with this name already exists
            if (!isset($methods[$method->getName()])) {
                $methods[$method->getName()] = $method;
            }
        }

        \ksort($methods, \SORT_NATURAL | \SORT_FLAG_CASE);

        return $methods;
    }

    /**
     * Prepare formatted method array.
     *
     * @param \ReflectionMethod[] $methods
     *
     * @return array
     */
    protected function prepareMethods(array $methods): array
    {
        // My kingdom for a generator.
        $ret = [];

        foreach ($methods as $name => $method) {
            if ($this->showItem($name)) {
                $ret[$name] = [
                    'name'  => $name,
                    'style' => $this->getVisibilityStyle($method),
                    'value' => $this->presentSignature($method),
                ];
            }
        }

        return $ret;
    }

    /**
     * Get a label for the particular kind of "class" represented.
     *
     * @param \ReflectionClass $reflector
     */
    protected function getKindLabel(\ReflectionClass $reflector): string
    {
        if ($reflector->isInterface()) {
            return 'Interface Methods';
        } elseif (\method_exists($reflector, 'isTrait') && $reflector->isTrait()) {
            return 'Trait Methods';
        } else {
            return 'Class Methods';
        }
    }

    /**
     * Get output style for the given method's visibility.
     *
     * @param \ReflectionMethod|ReflectionMagicMethod $method
     */
    private function getVisibilityStyle(\Reflector $method): string
    {
        if ($method instanceof ReflectionMagicMethod) {
            return self::IS_VIRTUAL;
        }

        if ($method->isPublic()) {
            return self::IS_PUBLIC;
        } elseif ($method->isProtected()) {
            return self::IS_PROTECTED;
        } else {
            return self::IS_PRIVATE;
        }
    }
}
