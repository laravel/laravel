<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\FatalErrorHandler;

use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\DebugClassLoader;
use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Symfony\Component\ClassLoader as SymfonyClassLoader;

/**
 * ErrorHandler for classes that do not exist.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ClassNotFoundFatalErrorHandler implements FatalErrorHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handleError(array $error, FatalErrorException $exception)
    {
        $messageLen = strlen($error['message']);
        $notFoundSuffix = '\' not found';
        $notFoundSuffixLen = strlen($notFoundSuffix);
        if ($notFoundSuffixLen > $messageLen) {
            return;
        }

        if (0 !== substr_compare($error['message'], $notFoundSuffix, -$notFoundSuffixLen)) {
            return;
        }

        foreach (array('class', 'interface', 'trait') as $typeName) {
            $prefix = ucfirst($typeName).' \'';
            $prefixLen = strlen($prefix);
            if (0 !== strpos($error['message'], $prefix)) {
                continue;
            }

            $fullyQualifiedClassName = substr($error['message'], $prefixLen, -$notFoundSuffixLen);
            if (false !== $namespaceSeparatorIndex = strrpos($fullyQualifiedClassName, '\\')) {
                $className = substr($fullyQualifiedClassName, $namespaceSeparatorIndex + 1);
                $namespacePrefix = substr($fullyQualifiedClassName, 0, $namespaceSeparatorIndex);
                $message = sprintf(
                    'Attempted to load %s "%s" from namespace "%s" in %s line %d. Do you need to "use" it from another namespace?',
                    $typeName,
                    $className,
                    $namespacePrefix,
                    $error['file'],
                    $error['line']
                );
            } else {
                $className = $fullyQualifiedClassName;
                $message = sprintf(
                    'Attempted to load %s "%s" from the global namespace in %s line %d. Did you forget a use statement for this %s?',
                    $typeName,
                    $className,
                    $error['file'],
                    $error['line'],
                    $typeName
                );
            }

            if ($classes = $this->getClassCandidates($className)) {
                $message .= sprintf(' Perhaps you need to add a use statement for one of the following: %s.', implode(', ', $classes));
            }

            return new ClassNotFoundException($message, $exception);
        }
    }

    /**
     * Tries to guess the full namespace for a given class name.
     *
     * By default, it looks for PSR-0 classes registered via a Symfony or a Composer
     * autoloader (that should cover all common cases).
     *
     * @param string $class A class name (without its namespace)
     *
     * @return array An array of possible fully qualified class names
     */
    private function getClassCandidates($class)
    {
        if (!is_array($functions = spl_autoload_functions())) {
            return array();
        }

        // find Symfony and Composer autoloaders
        $classes = array();
        foreach ($functions as $function) {
            if (!is_array($function)) {
                continue;
            }

            // get class loaders wrapped by DebugClassLoader
            if ($function[0] instanceof DebugClassLoader && method_exists($function[0], 'getClassLoader')) {
                $function[0] = $function[0]->getClassLoader();
            }

            if ($function[0] instanceof ComposerClassLoader || $function[0] instanceof SymfonyClassLoader) {
                foreach ($function[0]->getPrefixes() as $prefix => $paths) {
                    foreach ($paths as $path) {
                        $classes = array_merge($classes, $this->findClassInPath($path, $class, $prefix));
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * @param string $path
     * @param string $class
     * @param string $prefix
     *
     * @return array
     */
    private function findClassInPath($path, $class, $prefix)
    {
        if (!$path = realpath($path)) {
            return array();
        }

        $classes = array();
        $filename = $class.'.php';
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if ($filename == $file->getFileName() && $class = $this->convertFileToClass($path, $file->getPathName(), $prefix)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    /**
     * @param string $path
     * @param string $file
     * @param string $prefix
     *
     * @return string|null
     */
    private function convertFileToClass($path, $file, $prefix)
    {
        $candidates = array(
            // namespaced class
            $namespacedClass = str_replace(array($path.DIRECTORY_SEPARATOR, '.php', '/'), array('', '', '\\'), $file),
            // namespaced class (with target dir)
            $namespacedClassTargetDir = $prefix.str_replace(array($path.DIRECTORY_SEPARATOR, '.php', '/'), array('', '', '\\'), $file),
            // PEAR class
            str_replace('\\', '_', $namespacedClass),
            // PEAR class (with target dir)
            str_replace('\\', '_', $namespacedClassTargetDir),
        );

        // We cannot use the autoloader here as most of them use require; but if the class
        // is not found, the new autoloader call will require the file again leading to a
        // "cannot redeclare class" error.
        foreach ($candidates as $candidate) {
            if ($this->classExists($candidate)) {
                return $candidate;
            }
        }

        require_once $file;

        foreach ($candidates as $candidate) {
            if ($this->classExists($candidate)) {
                return $candidate;
            }
        }
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    private function classExists($class)
    {
        return class_exists($class, false) || interface_exists($class, false) || (function_exists('trait_exists') && trait_exists($class, false));
    }
}
