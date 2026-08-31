<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler\ErrorEnhancer;

use Composer\Autoload\ClassLoader;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;
use Symfony\Component\ErrorHandler\Error\FatalError;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ClassNotFoundErrorEnhancer implements ErrorEnhancerInterface
{
    public function enhance(\Throwable $error): ?\Throwable
    {
        // Some specific versions of PHP produce a fatal error when extending a not found class.
        $message = !$error instanceof FatalError ? $error->getMessage() : $error->getError()['message'];
        if (!preg_match('/^(Class|Interface|Trait) [\'"]([^\'"]+)[\'"] not found$/', $message, $matches)) {
            return null;
        }
        $typeName = strtolower($matches[1]);
        $fullyQualifiedClassName = $matches[2];

        if (false !== $namespaceSeparatorIndex = strrpos($fullyQualifiedClassName, '\\')) {
            $className = substr($fullyQualifiedClassName, $namespaceSeparatorIndex + 1);
            $namespacePrefix = substr($fullyQualifiedClassName, 0, $namespaceSeparatorIndex);
            $message = \sprintf('Attempted to load %s "%s" from namespace "%s".', $typeName, $className, $namespacePrefix);
            $tail = ' for another namespace?';
        } else {
            $className = $fullyQualifiedClassName;
            $message = \sprintf('Attempted to load %s "%s" from the global namespace.', $typeName, $className);
            $tail = '?';
        }

        if ($candidates = $this->getClassCandidates($className)) {
            $tail = array_pop($candidates).'"?';
            if ($candidates) {
                $tail = ' for e.g. "'.implode('", "', $candidates).'" or "'.$tail;
            } else {
                $tail = ' for "'.$tail;
            }
        }
        $message .= "\nDid you forget a \"use\" statement".$tail;

        return new ClassNotFoundError($message, $error);
    }

    /**
     * Tries to guess the full namespace for a given class name.
     *
     * By default, it looks for PSR-0 and PSR-4 classes registered via a Symfony or a Composer
     * autoloader (that should cover all common cases).
     *
     * @param string $class A class name (without its namespace)
     *
     * Returns an array of possible fully qualified class names
     */
    private function getClassCandidates(string $class): array
    {
        if (!\is_array($functions = spl_autoload_functions())) {
            return [];
        }

        // find Symfony and Composer autoloaders
        $classes = [];

        foreach ($functions as $function) {
            if (!\is_array($function)) {
                continue;
            }
            // get class loaders wrapped by DebugClassLoader
            if ($function[0] instanceof DebugClassLoader) {
                $function = $function[0]->getClassLoader();

                if (!\is_array($function)) {
                    continue;
                }
            }

            if ($function[0] instanceof ClassLoader) {
                foreach ($function[0]->getPrefixes() as $prefix => $paths) {
                    foreach ($paths as $path) {
                        $classes[] = $this->findClassInPath($path, $class, $prefix);
                    }
                }

                foreach ($function[0]->getPrefixesPsr4() as $prefix => $paths) {
                    foreach ($paths as $path) {
                        $classes[] = $this->findClassInPath($path, $class, $prefix);
                    }
                }
            }
        }

        return array_unique(array_merge([], ...$classes));
    }

    private function findClassInPath(string $path, string $class, string $prefix): array
    {
        $path = realpath($path.'/'.strtr($prefix, '\\_', '//')) ?: realpath($path.'/'.\dirname(strtr($prefix, '\\_', '//'))) ?: realpath($path);
        if (!$path || !is_dir($path)) {
            return [];
        }

        $classes = [];
        $filename = $class.'.php';
        try {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
                if ($filename == $file->getFileName() && $class = $this->convertFileToClass($path, $file->getPathName(), $prefix)) {
                    $classes[] = $class;
                }
            }
        } catch (\UnexpectedValueException) {
            // a subdirectory may vanish between listing and recursion (e.g. test fixtures)
        }

        return $classes;
    }

    private function convertFileToClass(string $path, string $file, string $prefix): ?string
    {
        $candidates = [
            // namespaced class
            $namespacedClass = str_replace([$path.\DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $file),
            // namespaced class (with target dir)
            $prefix.$namespacedClass,
            // namespaced class (with target dir and separator)
            $prefix.'\\'.$namespacedClass,
            // PEAR class
            str_replace('\\', '_', $namespacedClass),
            // PEAR class (with target dir)
            str_replace('\\', '_', $prefix.$namespacedClass),
            // PEAR class (with target dir and separator)
            str_replace('\\', '_', $prefix.'\\'.$namespacedClass),
        ];

        if ($prefix) {
            $candidates = array_filter($candidates, static fn ($candidate) => str_starts_with($candidate, $prefix));
        }

        // We cannot use the autoloader here as most of them use require; but if the class
        // is not found, the new autoloader call will require the file again leading to a
        // "cannot redeclare class" error.
        foreach ($candidates as $candidate) {
            if ($this->classExists($candidate)) {
                return $candidate;
            }
        }

        // Symfony may ship some polyfills, like "Normalizer". But if the Intl
        // extension is already installed, the next require_once will fail with
        // a compile error because the class is already defined. And this one
        // does not throw a Throwable. So it's better to skip it here.
        if (str_contains($file, 'Resources/stubs')) {
            return null;
        }

        try {
            require_once $file;
        } catch (\Throwable) {
            return null;
        }

        foreach ($candidates as $candidate) {
            if ($this->classExists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function classExists(string $class): bool
    {
        return class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false);
    }
}
