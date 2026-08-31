<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DependencyInjection;

use Composer\Autoload\ClassLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\HttpKernel\Kernel;

trigger_deprecation('symfony/http-kernel', '7.1', 'The "%s" class is deprecated since Symfony 7.1 and will be removed in 8.0.', AddAnnotatedClassesToCachePass::class);

/**
 * Sets the classes to compile in the cache for the container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since Symfony 7.1, to be removed in 8.0
 */
class AddAnnotatedClassesToCachePass implements CompilerPassInterface
{
    public function __construct(
        private Kernel $kernel,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        $annotatedClasses = [];
        foreach ($container->getExtensions() as $extension) {
            if ($extension instanceof Extension) {
                $annotatedClasses[] = $extension->getAnnotatedClassesToCompile();
            }
        }

        $annotatedClasses = array_merge($this->kernel->getAnnotatedClassesToCompile(), ...$annotatedClasses);

        $existingClasses = $this->getClassesInComposerClassMaps();

        $annotatedClasses = $container->getParameterBag()->resolveValue($annotatedClasses);
        $this->kernel->setAnnotatedClassCache($this->expandClasses($annotatedClasses, $existingClasses));
    }

    /**
     * Expands the given class patterns using a list of existing classes.
     *
     * @param array $patterns The class patterns to expand
     * @param array $classes  The existing classes to match against the patterns
     */
    private function expandClasses(array $patterns, array $classes): array
    {
        $expanded = [];

        // Explicit classes declared in the patterns are returned directly
        foreach ($patterns as $key => $pattern) {
            if (!str_ends_with($pattern, '\\') && !str_contains($pattern, '*')) {
                unset($patterns[$key]);
                $expanded[] = ltrim($pattern, '\\');
            }
        }

        // Match patterns with the classes list
        $regexps = $this->patternsToRegexps($patterns);

        foreach ($classes as $class) {
            $class = ltrim($class, '\\');

            if ($this->matchAnyRegexps($class, $regexps)) {
                $expanded[] = $class;
            }
        }

        return array_unique($expanded);
    }

    private function getClassesInComposerClassMaps(): array
    {
        $classes = [];

        foreach (spl_autoload_functions() as $function) {
            if (!\is_array($function)) {
                continue;
            }

            if ($function[0] instanceof DebugClassLoader) {
                $function = $function[0]->getClassLoader();
            }

            if (\is_array($function) && $function[0] instanceof ClassLoader) {
                $classes += array_filter($function[0]->getClassMap());
            }
        }

        return array_keys($classes);
    }

    private function patternsToRegexps(array $patterns): array
    {
        $regexps = [];

        foreach ($patterns as $pattern) {
            // Escape user input
            $regex = preg_quote(ltrim($pattern, '\\'));

            // Wildcards * and **
            $regex = strtr($regex, ['\\*\\*' => '.*?', '\\*' => '[^\\\\]*?']);

            // If this class does not end by a slash, anchor the end
            if (!str_ends_with($regex, '\\')) {
                $regex .= '$';
            }

            $regexps[] = '{^\\\\'.$regex.'}';
        }

        return $regexps;
    }

    private function matchAnyRegexps(string $class, array $regexps): bool
    {
        $isTest = str_contains($class, 'Test');

        foreach ($regexps as $regex) {
            if ($isTest && !str_contains($regex, 'Test')) {
                continue;
            }

            if (preg_match($regex, '\\'.$class)) {
                return true;
            }
        }

        return false;
    }
}
