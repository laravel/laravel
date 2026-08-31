<?php

namespace Illuminate\Support\Facades;

/**
 * @method static void compile(string|null $path = null)
 * @method static string getPath()
 * @method static void setPath(string $path)
 * @method static string compileString(string $value)
 * @method static string render(string $string, array $data = [], bool $deleteCachedView = false)
 * @method static string renderComponent(\Illuminate\View\Component $component)
 * @method static string stripParentheses(string $expression)
 * @method static void extend(callable $compiler)
 * @method static array getExtensions()
 * @method static void if(string $name, callable $callback)
 * @method static bool check(string $name, mixed ...$parameters)
 * @method static void component(string $class, string|null $alias = null, string $prefix = '')
 * @method static void components(array $components, string $prefix = '')
 * @method static array getClassComponentAliases()
 * @method static void anonymousComponentPath(string $path, string|null $prefix = null)
 * @method static void anonymousComponentNamespace(string $directory, string|null $prefix = null)
 * @method static void componentNamespace(string $namespace, string $prefix)
 * @method static array getAnonymousComponentPaths()
 * @method static array getAnonymousComponentNamespaces()
 * @method static array getClassComponentNamespaces()
 * @method static void aliasComponent(string $path, string|null $alias = null)
 * @method static void include(string $path, string|null $alias = null)
 * @method static void aliasInclude(string $path, string|null $alias = null)
 * @method static void bindDirective(string $name, callable $handler)
 * @method static void directive(string $name, \Closure|callable $handler, bool $bind = false)
 * @method static array getCustomDirectives()
 * @method static \Illuminate\View\Compilers\BladeCompiler prepareStringsForCompilationUsing(callable $callback)
 * @method static void precompiler(callable $precompiler)
 * @method static string usingEchoFormat(string $format, callable $callback)
 * @method static void setEchoFormat(string $format)
 * @method static void withDoubleEncoding()
 * @method static void withoutDoubleEncoding()
 * @method static void withoutComponentTags()
 * @method static string getCompiledPath(string $path)
 * @method static bool isExpired(string $path)
 * @method static string newComponentHash(string $component)
 * @method static string compileClassComponentOpening(string $component, string $alias, string $data, string $hash)
 * @method static string compileEndComponentClass()
 * @method static mixed sanitizeComponentAttribute(mixed $value)
 * @method static string compileEndOnce()
 * @method static void stringable(string|callable $class, callable|null $handler = null)
 * @method static string compileEchos(string $value)
 * @method static string applyEchoHandler(string $value)
 *
 * @see \Illuminate\View\Compilers\BladeCompiler
 */
class Blade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'blade.compiler';
    }
}
