<?php declare(strict_types=1);
/*
 * This file is part of sebastian/code-unit-reverse-lookup.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeUnitReverseLookup;

use function array_merge;
use function assert;
use function class_exists;
use function function_exists;
use function get_declared_classes;
use function get_declared_traits;
use function get_defined_functions;
use function is_array;
use function is_int;
use function is_string;
use function range;
use function trait_exists;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

final class Wizard
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $lookupTable = [];

    /**
     * @var array<class-string, true>
     */
    private array $processedClasses = [];

    /**
     * @var array<string, true>
     */
    private array $processedFunctions = [];

    public function lookup(string $filename, int $lineNumber): string
    {
        if (!isset($this->lookupTable[$filename][$lineNumber])) {
            $this->updateLookupTable();
        }

        if (isset($this->lookupTable[$filename][$lineNumber])) {
            return $this->lookupTable[$filename][$lineNumber];
        }

        return $filename . ':' . $lineNumber;
    }

    private function updateLookupTable(): void
    {
        $this->processClassesAndTraits();
        $this->processFunctions();
    }

    private function processClassesAndTraits(): void
    {
        $classes = get_declared_classes();
        $traits  = get_declared_traits();

        assert(is_array($traits));

        foreach (array_merge($classes, $traits) as $classOrTrait) {
            assert(class_exists($classOrTrait) || trait_exists($classOrTrait));

            if (isset($this->processedClasses[$classOrTrait])) {
                continue;
            }

            foreach ((new ReflectionClass($classOrTrait))->getMethods() as $method) {
                $this->processFunctionOrMethod($method);
            }

            $this->processedClasses[$classOrTrait] = true;
        }
    }

    private function processFunctions(): void
    {
        foreach (get_defined_functions()['user'] as $function) {
            assert(function_exists($function));

            if (isset($this->processedFunctions[$function])) {
                continue;
            }

            $this->processFunctionOrMethod(new ReflectionFunction($function));

            $this->processedFunctions[$function] = true;
        }
    }

    private function processFunctionOrMethod(ReflectionFunction|ReflectionMethod $functionOrMethod): void
    {
        if ($functionOrMethod->isInternal()) {
            return;
        }

        $name = $functionOrMethod->getName();

        if ($functionOrMethod instanceof ReflectionMethod) {
            $name = $functionOrMethod->getDeclaringClass()->getName() . '::' . $name;
        }

        $fileName = $functionOrMethod->getFileName();

        assert(is_string($fileName));

        if (!isset($this->lookupTable[$fileName])) {
            $this->lookupTable[$fileName] = [];
        }

        $startLine = $functionOrMethod->getStartLine();
        $endLine   = $functionOrMethod->getEndLine();

        assert(is_int($startLine));
        assert(is_int($endLine));
        assert($endLine >= $startLine);

        foreach (range($startLine, $endLine) as $line) {
            $this->lookupTable[$fileName][$line] = $name;
        }
    }
}
