<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler;

use Composer\InstalledVersions;
use Doctrine\Common\Persistence\Proxy as LegacyProxy;
use Doctrine\Persistence\Proxy;
use Mockery\MockInterface;
use Phake\IMock;
use PHPUnit\Framework\MockObject\Matcher\StatelessInvocation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Prophecy\Prophecy\ProphecySubjectInterface;
use ProxyManager\Proxy\ProxyInterface;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\Argument\LazyClosure;
use Symfony\Component\ErrorHandler\Internal\TentativeTypes;
use Symfony\Component\VarExporter\LazyObjectInterface;

/**
 * Autoloader checking if the class is really defined in the file found.
 *
 * The ClassLoader will wrap all registered autoloaders
 * and will throw an exception if a file is found but does
 * not declare the class.
 *
 * It can also patch classes to turn docblocks into actual return types.
 * This behavior is controlled by the SYMFONY_PATCH_TYPE_DECLARATIONS env var,
 * which is a url-encoded array with the follow parameters:
 *  - "force": any value enables deprecation notices - can be any of:
 *      - "phpdoc" to patch only docblock annotations
 *      - "2" to add all possible return types
 *      - "1" to add return types but only to tests/final/internal/private methods
 *  - "php": the target version of PHP - e.g. "7.1" doesn't generate "object" types
 *  - "deprecations": "1" to trigger a deprecation notice when a child class misses a
 *                    return type while the parent declares an "@return" annotation
 *
 * Note that patching doesn't care about any coding style so you'd better to run
 * php-cs-fixer after, with rules "phpdoc_trim_consecutive_blank_line_separation"
 * and "no_superfluous_phpdoc_tags" enabled typically.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Christophe Coevoet <stof@notk.org>
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Guilhem Niot <guilhem.niot@gmail.com>
 */
class DebugClassLoader
{
    private const SPECIAL_RETURN_TYPES = [
        'void' => 'void',
        'null' => 'null',
        'resource' => 'resource',
        'boolean' => 'bool',
        'true' => 'true',
        'false' => 'false',
        'integer' => 'int',
        'array' => 'array',
        'bool' => 'bool',
        'callable' => 'callable',
        'float' => 'float',
        'int' => 'int',
        'iterable' => 'iterable',
        'object' => 'object',
        'string' => 'string',
        'non-empty-string' => 'string',
        'self' => 'self',
        'parent' => 'parent',
        'mixed' => 'mixed',
        'static' => 'static',
        '$this' => 'static',
        'list' => 'array',
        'non-empty-list' => 'array',
        'class-string' => 'string',
        'never' => 'never',
    ];

    private const BUILTIN_RETURN_TYPES = [
        'void' => true,
        'array' => true,
        'false' => true,
        'bool' => true,
        'callable' => true,
        'float' => true,
        'int' => true,
        'iterable' => true,
        'object' => true,
        'string' => true,
        'self' => true,
        'parent' => true,
        'mixed' => true,
        'static' => true,
        'null' => true,
        'true' => true,
        'never' => true,
    ];

    private const MAGIC_METHODS = [
        '__isset' => 'bool',
        '__sleep' => 'array',
        '__toString' => 'string',
        '__debugInfo' => 'array',
        '__serialize' => 'array',
        '__set' => 'void',
        '__unset' => 'void',
        '__unserialize' => 'void',
        '__wakeup' => 'void',
    ];

    /**
     * @var callable
     */
    private $classLoader;
    private bool $isFinder;
    private array $loaded = [];
    private array $patchTypes = [];

    private static int $caseCheck;
    private static array $checkedClasses = [];
    private static array $final = [];
    private static array $finalMethods = [];
    private static array $finalProperties = [];
    private static array $finalConstants = [];
    private static array $deprecated = [];
    private static array $internal = [];
    private static array $internalMethods = [];
    private static array $annotatedParameters = [];
    private static array $darwinCache = ['/' => ['/', []]];
    /**
     * @var array<string, list<array{0: string, 1: bool, 2: string, 3: string, 4: string|null}>>
     *
     * Maps an interface FQCN (or an abstract class accumulating entries from its interfaces) to the list of
     * "@method" annotations declared on it. For interfaces, the entry is populated directly by parsing the
     * annotations from the interface's docblock. For abstract classes, the information from all implemented
     * interfaces is merged together, so that the check can later be applied to the first concrete subclass.
     *
     * Each entry is a tuple of:
     *   [0] string      $interface   - FQCN of the interface that carries the "@method" annotation
     *   [1] bool        $static      - whether the method is declared static
     *   [2] string      $returnType  - return type from the annotation, or '' if absent
     *   [3] string      $name        - method name plus its parameter signature, e.g. "foo($arg, int $n)"
     *   [4] string|null $description - description text (period-normalised), or null if absent
     */
    private static array $method = [];
    private static array $returnTypes = [];
    private static array $methodTraits = [];
    private static array $fileOffsets = [];

    public function __construct(callable $classLoader)
    {
        $this->classLoader = $classLoader;
        $this->isFinder = \is_array($classLoader) && method_exists($classLoader[0], 'findFile');
        parse_str($_ENV['SYMFONY_PATCH_TYPE_DECLARATIONS'] ?? $_SERVER['SYMFONY_PATCH_TYPE_DECLARATIONS'] ?? getenv('SYMFONY_PATCH_TYPE_DECLARATIONS') ?: '', $this->patchTypes);
        $this->patchTypes += [
            'force' => null,
            'php' => \PHP_MAJOR_VERSION.'.'.\PHP_MINOR_VERSION,
            'deprecations' => true,
        ];

        if ('phpdoc' === $this->patchTypes['force']) {
            $this->patchTypes['force'] = 'docblock';
        }

        if (!isset(self::$caseCheck)) {
            $file = is_file(__FILE__) ? __FILE__ : rtrim(realpath('.'), \DIRECTORY_SEPARATOR);
            $i = strrpos($file, \DIRECTORY_SEPARATOR);
            $dir = substr($file, 0, 1 + $i);
            $file = substr($file, 1 + $i);
            $test = strtoupper($file) === $file ? strtolower($file) : strtoupper($file);
            $test = realpath($dir.$test);

            if (false === $test || false === $i) {
                // filesystem is case-sensitive
                self::$caseCheck = 0;
            } elseif (str_ends_with($test, $file)) {
                // filesystem is case-insensitive and realpath() normalizes the case of characters
                self::$caseCheck = 1;
            } elseif ('Darwin' === \PHP_OS_FAMILY) {
                // on MacOSX, HFS+ is case-insensitive but realpath() doesn't normalize the case of characters
                self::$caseCheck = 2;
            } else {
                // filesystem case checks failed, fallback to disabling them
                self::$caseCheck = 0;
            }
        }
    }

    public function getClassLoader(): callable
    {
        return $this->classLoader;
    }

    /**
     * Wraps all autoloaders.
     */
    public static function enable(): void
    {
        // Ensures we don't hit https://bugs.php.net/42098
        class_exists(ErrorHandler::class);
        class_exists(LogLevel::class);

        if (!\is_array($functions = spl_autoload_functions())) {
            return;
        }

        foreach ($functions as $function) {
            spl_autoload_unregister($function);
        }

        foreach ($functions as $function) {
            if (!\is_array($function) || !$function[0] instanceof self) {
                $function = [new static($function), 'loadClass'];
            }

            spl_autoload_register($function);
        }
    }

    /**
     * Disables the wrapping.
     */
    public static function disable(): void
    {
        if (!\is_array($functions = spl_autoload_functions())) {
            return;
        }

        foreach ($functions as $function) {
            spl_autoload_unregister($function);
        }

        foreach ($functions as $function) {
            if (\is_array($function) && $function[0] instanceof self) {
                $function = $function[0]->getClassLoader();
            }

            spl_autoload_register($function);
        }
    }

    public static function checkClasses(): bool
    {
        if (!\is_array($functions = spl_autoload_functions())) {
            return false;
        }

        $loader = null;

        foreach ($functions as $function) {
            if (\is_array($function) && $function[0] instanceof self) {
                $loader = $function[0];
                break;
            }
        }

        if (null === $loader) {
            return false;
        }

        static $offsets = [
            'get_declared_interfaces' => 0,
            'get_declared_traits' => 0,
            'get_declared_classes' => 0,
        ];

        foreach ($offsets as $getSymbols => $i) {
            $symbols = $getSymbols();

            for (; $i < \count($symbols); ++$i) {
                if (!is_subclass_of($symbols[$i], MockObject::class)
                    && !is_subclass_of($symbols[$i], Stub::class)
                    && !is_subclass_of($symbols[$i], ProphecySubjectInterface::class)
                    && !is_subclass_of($symbols[$i], Proxy::class)
                    && !is_subclass_of($symbols[$i], ProxyInterface::class)
                    && !is_subclass_of($symbols[$i], LazyObjectInterface::class)
                    && !is_subclass_of($symbols[$i], LegacyProxy::class)
                    && !is_subclass_of($symbols[$i], MockInterface::class)
                    && !is_subclass_of($symbols[$i], IMock::class)
                    && !(is_subclass_of($symbols[$i], LazyClosure::class) && str_contains($symbols[$i], "@anonymous\0"))
                ) {
                    $loader->checkClass($symbols[$i]);
                }
            }

            $offsets[$getSymbols] = $i;
        }

        return true;
    }

    public function findFile(string $class): ?string
    {
        return $this->isFinder ? ($this->classLoader[0]->findFile($class) ?: null) : null;
    }

    /**
     * Loads the given class or interface.
     *
     * @throws \RuntimeException
     */
    public function loadClass(string $class): void
    {
        $e = error_reporting(error_reporting() | \E_PARSE | \E_ERROR | \E_CORE_ERROR | \E_COMPILE_ERROR);

        try {
            if ($this->isFinder && !isset($this->loaded[$class])) {
                $this->loaded[$class] = true;
                if (!$file = $this->classLoader[0]->findFile($class) ?: '') {
                    // no-op
                } elseif (\function_exists('opcache_is_script_cached') && @opcache_is_script_cached($file)) {
                    include $file;

                    return;
                } elseif (false === include $file) {
                    return;
                }
            } else {
                ($this->classLoader)($class);
                $file = '';
            }
        } finally {
            error_reporting($e);
        }

        $this->checkClass($class, $file);
    }

    private function checkClass(string $class, ?string $file = null): void
    {
        $exists = null === $file || class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false);

        if (null !== $file && $class && '\\' === $class[0]) {
            $class = substr($class, 1);
        }

        if ($exists) {
            if (isset(self::$checkedClasses[$class])) {
                return;
            }
            self::$checkedClasses[$class] = true;

            $refl = new \ReflectionClass($class);
            if (null === $file && $refl->isInternal()) {
                return;
            }
            $name = $refl->getName();

            if ($name !== $class && 0 === strcasecmp($name, $class)) {
                throw new \RuntimeException(\sprintf('Case mismatch between loaded and declared class names: "%s" vs "%s".', $class, $name));
            }

            $deprecations = $this->checkAnnotations($refl, $name);

            foreach ($deprecations as $message) {
                @trigger_error($message, \E_USER_DEPRECATED);
            }
        }

        if (!$file) {
            return;
        }

        if (!$exists) {
            if (str_contains($class, '/')) {
                throw new \RuntimeException(\sprintf('Trying to autoload a class with an invalid name "%s". Be careful that the namespace separator is "\" in PHP, not "/".', $class));
            }

            throw new \RuntimeException(\sprintf('The autoloader expected class "%s" to be defined in file "%s". The file was found but the class was not in it, the class name or namespace probably has a typo.', $class, $file));
        }

        if (self::$caseCheck && $message = $this->checkCase($refl, $file, $class)) {
            throw new \RuntimeException(\sprintf('Case mismatch between class and real file names: "%s" vs "%s" in "%s".', $message[0], $message[1], $message[2]));
        }
    }

    public function checkAnnotations(\ReflectionClass $refl, string $class): array
    {
        if (
            'Symfony\Bridge\PhpUnit\Legacy\SymfonyTestsListenerForV7' === $class
            || 'Symfony\Bridge\PhpUnit\Legacy\SymfonyTestsListenerForV6' === $class
        ) {
            return [];
        }
        $deprecations = [];

        $className = str_contains($class, "@anonymous\0") ? (get_parent_class($class) ?: key(class_implements($class)) ?: 'class').'@anonymous' : $class;

        // Don't trigger deprecations for classes in the same vendor
        if ($class !== $className) {
            $vendor = $refl->getFileName() && preg_match('/^namespace ([^;\\\\\s]++)[;\\\\]/m', @file_get_contents($refl->getFileName()) ?: '', $vendor) ? $vendor[1].'\\' : '';
            $vendorLen = \strlen($vendor);
        } elseif (2 > $vendorLen = 1 + (strpos($class, '\\') ?: strpos($class, '_'))) {
            $vendorLen = 0;
            $vendor = '';
        } else {
            $vendor = str_replace('_', '\\', substr($class, 0, $vendorLen));
        }

        $parent = get_parent_class($class) ?: null;
        self::$returnTypes[$class] = [];
        $classIsTemplate = false;

        // Detect annotations on the class
        if ($doc = $this->parsePhpDoc($refl)) {
            $classIsTemplate = isset($doc['template']) || isset($doc['template-covariant']);

            foreach (['final', 'deprecated', 'internal'] as $annotation) {
                if (null !== $description = $doc[$annotation][0] ?? null) {
                    self::${$annotation}[$class] = '' !== $description ? ' '.$description.(preg_match('/[.!]$/', $description) ? '' : '.') : '.';
                }
            }

            if ($refl->isInterface() && isset($doc['method'])) {
                foreach ($doc['method'] as $name => [$static, $returnType, $signature, $description]) {
                    if ($refl->hasMethod($static ? '__callStatic' : '__call')) {
                        // When the interface has "virtual" @method declarations but at the same time contains a __call/__callStatic magic method,
                        // do not trigger a deprecation notice. This is to address special use cases like in Predis' ClientInterface where the
                        // "@method" annotations never intend to actually add the method to the interface, but are used to document the "virtual"
                        // API provided by the interface through the technical implementation of magic calls. This might cause false negatives
                        // (missing notices) in the case that such interfaces are later amended with actual (real) methods.
                        continue;
                    }
                    self::$method[$class][] = [$class, $static, $returnType, $name.$signature, $description];

                    if ('' !== $returnType) {
                        $this->setReturnType($returnType, $refl->name, $name, $refl->getFileName(), $parent);
                    }
                }
            }
        }

        $parentAndOwnInterfaces = $this->getOwnInterfaces($class, $parent);
        if ($parent) {
            $parentAndOwnInterfaces[$parent] = $parent;

            if (!isset(self::$checkedClasses[$parent])) {
                $this->checkClass($parent);
            }

            if (isset(self::$final[$parent])) {
                $deprecations[] = \sprintf('The "%s" class is considered final%s It may change without further notice as of its next major version. You should not extend it from "%s".', $parent, self::$final[$parent], $className);
            }
        }

        // When the parent is a concrete class, we will trigger deprecation notices to make it aware that it needs
        // to add the new methods announced with @method. The parent will have to provide all those methods.
        // For child classes this means they will not need to deal with @method coming from any of the interfaces
        // the parent implements.
        // Put those interfaces that we can ignore into $parentInterfaces.
        // The ternary makes use of the fact that abstract parent classes will accumulate the methods in self::$method,
        // so !isset(self::$method[$parent]) indicates a concrete parent class.
        $parentInterfaces = ($parent && !isset(self::$method[$parent])) ? class_implements($parent, false) : [];

        // Detect if the parent is annotated
        foreach ($parentAndOwnInterfaces + class_uses($class, false) as $use) {
            if (!isset(self::$checkedClasses[$use])) {
                $this->checkClass($use);
            }
            if (isset(self::$deprecated[$use]) && strncmp($vendor, str_replace('_', '\\', $use), $vendorLen) && !isset(self::$deprecated[$class])) {
                $type = class_exists($class, false) ? 'class' : (interface_exists($class, false) ? 'interface' : 'trait');
                $verb = class_exists($use, false) || interface_exists($class, false) ? 'extends' : (interface_exists($use, false) ? 'implements' : 'uses');

                $deprecations[] = \sprintf('The "%s" %s %s "%s" that is deprecated%s', $className, $type, $verb, $use, self::$deprecated[$use]);
            }
            if (isset(self::$internal[$use]) && strncmp($vendor, str_replace('_', '\\', $use), $vendorLen)) {
                $deprecations[] = \sprintf('The "%s" %s is considered internal%s It may change without further notice. You should not use it from "%s".', $use, class_exists($use, false) ? 'class' : (interface_exists($use, false) ? 'interface' : 'trait'), self::$internal[$use], $className);
            }
            if (isset(self::$method[$use])) {
                if ($refl->isAbstract() || $refl->isInterface()) {
                    // Abstract classes and interfaces inherit @method from interfaces they
                    // implement directly or through inheritance.
                    if (isset(self::$method[$class])) {
                        self::$method[$class] = array_merge(self::$method[$class], self::$method[$use]);
                    } else {
                        self::$method[$class] = self::$method[$use];
                    }
                } else {
                    if (!strncmp($vendor, str_replace('_', '\\', $use), $vendorLen)
                        && str_starts_with($className, 'Symfony\\')
                        && (!class_exists(InstalledVersions::class)
                            || 'symfony/symfony' !== InstalledVersions::getRootPackage()['name'])
                    ) {
                        // skip "same vendor" @method deprecations for Symfony\* classes unless symfony/symfony is being tested
                        continue;
                    }
                    foreach (self::$method[$use] as [$interface, $static, $returnType, $name, $description]) {
                        if (isset($parentInterfaces[$interface])) {
                            // The @method annotation comes from an interface that has already been implemented by a concrete parent class,
                            // so we can ignore it here.
                            continue;
                        }
                        $realName = substr($name, 0, strpos($name, '('));
                        if (!$refl->hasMethod($realName) || !($methodRefl = $refl->getMethod($realName))->isPublic() || ($static xor $methodRefl->isStatic())) {
                            $deprecations[] = \sprintf('Class "%s" should implement method "%s::%s%s"%s', $className, ($static ? 'static ' : '').$interface, $name, $returnType ? ': '.$returnType : '', null === $description ? '.' : ': '.$description);
                        }
                    }
                }
            }
        }

        if (trait_exists($class)) {
            $file = $refl->getFileName();

            foreach ($refl->getMethods() as $method) {
                if ($method->getFileName() === $file) {
                    self::$methodTraits[$file][$method->getStartLine()] = $class;
                }
            }

            return $deprecations;
        }

        // Inherit @final, @internal, @param and @return annotations for methods
        self::$finalMethods[$class] = [];
        self::$internalMethods[$class] = [];
        self::$annotatedParameters[$class] = [];
        self::$finalProperties[$class] = [];
        self::$finalConstants[$class] = [];
        foreach ($parentAndOwnInterfaces as $use) {
            foreach (['finalMethods', 'internalMethods', 'annotatedParameters', 'returnTypes', 'finalProperties', 'finalConstants'] as $property) {
                if (isset(self::${$property}[$use])) {
                    self::${$property}[$class] = self::${$property}[$class] ? self::${$property}[$use] + self::${$property}[$class] : self::${$property}[$use];
                }
            }

            if (null !== (TentativeTypes::RETURN_TYPES[$use] ?? null)) {
                foreach (TentativeTypes::RETURN_TYPES[$use] as $method => $returnType) {
                    $returnType = explode('|', $returnType);
                    foreach ($returnType as $i => $t) {
                        if ('?' !== $t && !isset(self::BUILTIN_RETURN_TYPES[$t])) {
                            $returnType[$i] = '\\'.$t;
                        }
                    }
                    $returnType = implode('|', $returnType);

                    self::$returnTypes[$class] += [$method => [$returnType, str_starts_with($returnType, '?') ? substr($returnType, 1).'|null' : $returnType, $use, '']];
                }
            }
        }

        foreach ($refl->getMethods() as $method) {
            if ($method->class !== $class) {
                continue;
            }

            if (null === $ns = self::$methodTraits[$method->getFileName()][$method->getStartLine()] ?? null) {
                $ns = $vendor;
                $len = $vendorLen;
            } elseif (2 > $len = 1 + (strpos($ns, '\\') ?: strpos($ns, '_'))) {
                $len = 0;
                $ns = '';
            } else {
                $ns = str_replace('_', '\\', substr($ns, 0, $len));
            }

            if ($parent && isset(self::$finalMethods[$parent][$method->name])) {
                [$declaringClass, $message] = self::$finalMethods[$parent][$method->name];
                $deprecations[] = \sprintf('The "%s::%s()" method is considered final%s It may change without further notice as of its next major version. You should not extend it from "%s".', $declaringClass, $method->name, $message, $className);
            }

            if (isset(self::$internalMethods[$class][$method->name])) {
                [$declaringClass, $message] = self::$internalMethods[$class][$method->name];
                if (strncmp($ns, $declaringClass, $len)) {
                    $deprecations[] = \sprintf('The "%s::%s()" method is considered internal%s It may change without further notice. You should not extend it from "%s".', $declaringClass, $method->name, $message, $className);
                }
            }

            // To read method annotations
            $doc = $this->parsePhpDoc($method);

            if (($classIsTemplate || isset($doc['template']) || isset($doc['template-covariant'])) && $method->hasReturnType()) {
                unset($doc['return']);
            }

            if (isset(self::$annotatedParameters[$class][$method->name])) {
                $definedParameters = [];
                foreach ($method->getParameters() as $parameter) {
                    $definedParameters[$parameter->name] = true;
                }

                foreach (self::$annotatedParameters[$class][$method->name] as $parameterName => $deprecation) {
                    if (!isset($definedParameters[$parameterName]) && !isset($doc['param'][$parameterName])) {
                        $deprecations[] = \sprintf($deprecation, $className);
                    }
                }
            }

            $forcePatchTypes = $this->patchTypes['force'];

            if ($canAddReturnType = null !== $forcePatchTypes && !str_contains($method->getFileName(), \DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR)) {
                $this->patchTypes['force'] = $forcePatchTypes ?: 'docblock';

                $canAddReturnType = 2 === (int) $forcePatchTypes
                    || false !== stripos($method->getFileName(), \DIRECTORY_SEPARATOR.'Tests'.\DIRECTORY_SEPARATOR)
                    || $refl->isFinal()
                    || $method->isFinal()
                    || $method->isPrivate()
                    || ('.' === (self::$internal[$class] ?? null) && !$refl->isAbstract())
                    || '.' === (self::$final[$class] ?? null)
                    || '' === ($doc['final'][0] ?? null)
                    || '' === ($doc['internal'][0] ?? null)
                ;
            }

            if (null !== ($returnType = self::$returnTypes[$class][$method->name] ?? null) && 'docblock' === $this->patchTypes['force'] && !$method->hasReturnType() && isset(TentativeTypes::RETURN_TYPES[$returnType[2]][$method->name])) {
                $this->patchReturnTypeWillChange($method);
            }

            if (null !== ($returnType ??= self::MAGIC_METHODS[$method->name] ?? null) && !$method->hasReturnType() && !isset($doc['return'])) {
                [$normalizedType, $returnType, $declaringClass, $declaringFile] = \is_string($returnType) ? [$returnType, $returnType, '', ''] : $returnType;

                if ($canAddReturnType && 'docblock' !== $this->patchTypes['force']) {
                    $this->patchMethod($method, $returnType, $declaringFile, $normalizedType);
                }
                if (!isset($doc['deprecated']) && strncmp($ns, $declaringClass, $len)) {
                    if ('docblock' === $this->patchTypes['force']) {
                        $this->patchMethod($method, $returnType, $declaringFile, $normalizedType);
                    } elseif ('' !== $declaringClass && $this->patchTypes['deprecations']) {
                        $deprecations[] = \sprintf('Method "%s::%s()" might add "%s" as a native return type declaration in the future. Do the same in %s "%s" now to avoid errors or add an explicit @return annotation to suppress this message.', $declaringClass, $method->name, $normalizedType, interface_exists($declaringClass) ? 'implementation' : 'child class', $className);
                    }
                }
            }

            if (!$doc) {
                $this->patchTypes['force'] = $forcePatchTypes;

                continue;
            }

            if (isset($doc['return'])) {
                $this->setReturnType($doc['return'] ?? self::MAGIC_METHODS[$method->name], $method->class, $method->name, $method->getFileName(), $parent, $method->getReturnType());

                if (isset(self::$returnTypes[$class][$method->name][0]) && $canAddReturnType) {
                    $this->fixReturnStatements($method, self::$returnTypes[$class][$method->name][0]);
                }

                if ($method->isPrivate()) {
                    unset(self::$returnTypes[$class][$method->name]);
                }
            }

            $this->patchTypes['force'] = $forcePatchTypes;

            if ($method->isPrivate()) {
                continue;
            }

            $finalOrInternal = false;

            foreach (['final', 'internal'] as $annotation) {
                if (null !== $description = $doc[$annotation][0] ?? null) {
                    self::${$annotation.'Methods'}[$class][$method->name] = [$class, '' !== $description ? ' '.$description.(preg_match('/[[:punct:]]$/', $description) ? '' : '.') : '.'];
                    $finalOrInternal = true;
                }
            }

            if ($finalOrInternal || $method->isConstructor() || !isset($doc['param']) || StatelessInvocation::class === $class) {
                continue;
            }
            if (!isset(self::$annotatedParameters[$class][$method->name])) {
                $definedParameters = [];
                foreach ($method->getParameters() as $parameter) {
                    $definedParameters[$parameter->name] = true;
                }
            }
            foreach ($doc['param'] as $parameterName => $parameterType) {
                if (!isset($definedParameters[$parameterName])) {
                    self::$annotatedParameters[$class][$method->name][$parameterName] = \sprintf('The "%%s::%s()" method will require a new "%s$%s" argument in the next major version of its %s "%s", not defining it is deprecated.', $method->name, $parameterType ? $parameterType.' ' : '', $parameterName, interface_exists($className) ? 'interface' : 'parent class', $className);
                }
            }
        }

        $finals = isset(self::$final[$class]) || $refl->isFinal() ? [] : [
            'finalConstants' => $refl->getReflectionConstants(\ReflectionClassConstant::IS_PUBLIC | \ReflectionClassConstant::IS_PROTECTED),
            'finalProperties' => $refl->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED),
        ];
        foreach ($finals as $type => $reflectors) {
            foreach ($reflectors as $r) {
                if ($r->class !== $class) {
                    continue;
                }

                $doc = $this->parsePhpDoc($r);

                foreach ($parentAndOwnInterfaces as $use) {
                    if (isset(self::${$type}[$use][$r->name]) && !isset($doc['deprecated']) && ('finalConstants' === $type || substr($use, 0, strrpos($use, '\\')) !== substr($use, 0, strrpos($class, '\\')))) {
                        $msg = 'finalConstants' === $type ? '%s" constant' : '$%s" property';
                        $deprecations[] = \sprintf('The "%s::'.$msg.' is considered final. You should not override it in "%s".', self::${$type}[$use][$r->name], $r->name, $class);
                    }
                }

                if (isset($doc['final']) || ('finalProperties' === $type && str_starts_with($class, 'Symfony\\') && !$r->hasType())) {
                    self::${$type}[$class][$r->name] = $class;
                }
            }
        }

        return $deprecations;
    }

    public function checkCase(\ReflectionClass $refl, string $file, string $class): ?array
    {
        $real = explode('\\', $class.strrchr($file, '.'));
        $tail = explode(\DIRECTORY_SEPARATOR, str_replace('/', \DIRECTORY_SEPARATOR, $file));

        $i = \count($tail) - 1;
        $j = \count($real) - 1;

        while (isset($tail[$i], $real[$j]) && $tail[$i] === $real[$j]) {
            --$i;
            --$j;
        }

        array_splice($tail, 0, $i + 1);

        if (!$tail) {
            return null;
        }

        $tail = \DIRECTORY_SEPARATOR.implode(\DIRECTORY_SEPARATOR, $tail);
        $tailLen = \strlen($tail);
        $real = $refl->getFileName();

        if (2 === self::$caseCheck) {
            $real = $this->darwinRealpath($real);
        }

        if (0 === substr_compare($real, $tail, -$tailLen, $tailLen, true)
            && 0 !== substr_compare($real, $tail, -$tailLen, $tailLen, false)
        ) {
            return [substr($tail, -$tailLen + 1), substr($real, -$tailLen + 1), substr($real, 0, -$tailLen + 1)];
        }

        return null;
    }

    /**
     * `realpath` on MacOSX doesn't normalize the case of characters.
     */
    private function darwinRealpath(string $real): string
    {
        $i = 1 + strrpos($real, '/');
        $file = substr($real, $i);
        $real = substr($real, 0, $i);

        if (isset(self::$darwinCache[$real])) {
            $kDir = $real;
        } else {
            $kDir = strtolower($real);

            if (isset(self::$darwinCache[$kDir])) {
                $real = self::$darwinCache[$kDir][0];
            } else {
                $dir = getcwd();

                if (!@chdir($real)) {
                    return $real.$file;
                }

                $real = getcwd().'/';
                chdir($dir);

                $dir = $real;
                $k = $kDir;
                $i = \strlen($dir) - 1;
                while (!isset(self::$darwinCache[$k])) {
                    self::$darwinCache[$k] = [$dir, []];
                    self::$darwinCache[$dir] = &self::$darwinCache[$k];

                    while ('/' !== $dir[--$i]) {
                    }
                    $k = substr($k, 0, ++$i);
                    $dir = substr($dir, 0, $i--);
                }
            }
        }

        $dirFiles = self::$darwinCache[$kDir][1];

        if (!isset($dirFiles[$file]) && str_ends_with($file, ') : eval()\'d code')) {
            // Get the file name from "file_name.php(123) : eval()'d code"
            $file = substr($file, 0, strrpos($file, '(', -17));
        }

        if (isset($dirFiles[$file])) {
            return $real.$dirFiles[$file];
        }

        $kFile = strtolower($file);

        if (!isset($dirFiles[$kFile])) {
            foreach (scandir($real, 2) as $f) {
                if ('.' !== $f[0]) {
                    $dirFiles[$f] = $f;
                    if ($f === $file) {
                        $kFile = $file;
                    } elseif ($f !== $k = strtolower($f)) {
                        $dirFiles[$k] = $f;
                    }
                }
            }
            self::$darwinCache[$kDir][1] = $dirFiles;
        }

        return $real.$dirFiles[$kFile];
    }

    /**
     * `class_implements` includes interfaces from the parents so we have to manually exclude them.
     *
     * @return string[]
     */
    private function getOwnInterfaces(string $class, ?string $parent): array
    {
        $ownInterfaces = class_implements($class, false);

        if ($parent) {
            foreach (class_implements($parent, false) as $interface) {
                unset($ownInterfaces[$interface]);
            }
        }

        foreach ($ownInterfaces as $interface) {
            foreach (class_implements($interface) as $interface) {
                unset($ownInterfaces[$interface]);
            }
        }

        return $ownInterfaces;
    }

    private function setReturnType(string $types, string $class, string $method, string $filename, ?string $parent, ?\ReflectionType $returnType = null): void
    {
        if ('__construct' === $method) {
            return;
        }

        if ('null' === $types) {
            self::$returnTypes[$class][$method] = ['null', 'null', $class, $filename];

            return;
        }

        if ($nullable = str_starts_with($types, 'null|')) {
            $types = substr($types, 5);
        } elseif ($nullable = str_ends_with($types, '|null')) {
            $types = substr($types, 0, -5);
        }
        $arrayType = ['array' => 'array'];
        $typesMap = [];
        $glue = str_contains($types, '&') ? '&' : '|';
        foreach (explode($glue, $types) as $t) {
            $t = self::SPECIAL_RETURN_TYPES[strtolower($t)] ?? $t;
            $typesMap[$this->normalizeType($t, $class, $parent, $returnType)][$t] = $t;
        }

        if (isset($typesMap['array'])) {
            if (isset($typesMap['Traversable']) || isset($typesMap['\Traversable'])) {
                $typesMap['iterable'] = $arrayType !== $typesMap['array'] ? $typesMap['array'] : ['iterable'];
                unset($typesMap['array'], $typesMap['Traversable'], $typesMap['\Traversable']);
            } elseif ($arrayType !== $typesMap['array'] && isset(self::$returnTypes[$class][$method]) && !$returnType) {
                return;
            }
        }

        if (isset($typesMap['array']) && isset($typesMap['iterable'])) {
            if ($arrayType !== $typesMap['array']) {
                $typesMap['iterable'] = $typesMap['array'];
            }
            unset($typesMap['array']);
        }

        $iterable = $object = true;
        foreach ($typesMap as $n => $t) {
            if ('null' !== $n) {
                $iterable = $iterable && (\in_array($n, ['array', 'iterable'], true) || str_contains($n, 'Iterator'));
                $object = $object && (\in_array($n, ['callable', 'object', '$this', 'static'], true) || !isset(self::SPECIAL_RETURN_TYPES[$n]));
            }
        }

        $phpTypes = [];
        $docTypes = [];

        foreach ($typesMap as $n => $t) {
            if (str_contains($n, '::')) {
                [$definingClass, $constantName] = explode('::', $n, 2);
                $definingClass = match ($definingClass) {
                    'self', 'static', 'parent' => $class,
                    default => $definingClass,
                };

                if (!\defined($definingClass.'::'.$constantName)) {
                    return;
                }

                $constant = new \ReflectionClassConstant($definingClass, $constantName);

                if (\PHP_VERSION_ID >= 80300 && $constantType = $constant->getType()) {
                    if ($constantType instanceof \ReflectionNamedType) {
                        $n = $constantType->getName();
                    } else {
                        return;
                    }
                } else {
                    $n = \gettype($constant->getValue());
                }
            }

            if ('null' === $n) {
                $nullable = true;
                continue;
            }

            $docTypes[] = $t;

            if ('mixed' === $n || 'void' === $n) {
                $nullable = false;
                $phpTypes = ['' => $n];
                continue;
            }

            if ('resource' === $n) {
                // there is no native type for "resource"
                return;
            }

            if (!preg_match('/^(?:\\\\?[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)+$/', $n)) {
                // exclude any invalid PHP class name (e.g. `Cookie::SAMESITE_*`)
                continue;
            }

            if (!isset($phpTypes['']) && !\in_array($n, $phpTypes, true)) {
                $phpTypes[] = $n;
            }
        }
        $docTypes = array_merge([], ...$docTypes);

        if (!$phpTypes) {
            return;
        }

        if (1 < \count($phpTypes)) {
            if ($iterable && '8.0' > $this->patchTypes['php']) {
                $phpTypes = $docTypes = ['iterable'];
            } elseif ($object && 'object' === $this->patchTypes['force']) {
                $phpTypes = $docTypes = ['object'];
            } elseif ('8.0' > $this->patchTypes['php']) {
                // ignore multi-types return declarations
                return;
            }
        }

        $phpType = \sprintf($nullable ? (1 < \count($phpTypes) ? '%s|null' : '?%s') : '%s', implode($glue, $phpTypes));
        $docType = \sprintf($nullable ? '%s|null' : '%s', implode($glue, $docTypes));

        self::$returnTypes[$class][$method] = [$phpType, $docType, $class, $filename];
    }

    private function normalizeType(string $type, string $class, ?string $parent, ?\ReflectionType $returnType): string
    {
        if (isset(self::SPECIAL_RETURN_TYPES[$lcType = strtolower($type)])) {
            if ('parent' === $lcType = self::SPECIAL_RETURN_TYPES[$lcType]) {
                $lcType = null !== $parent ? '\\'.$parent : 'parent';
            } elseif ('self' === $lcType) {
                $lcType = '\\'.$class;
            }

            return $lcType;
        }

        // We could resolve "use" statements to return the FQDN
        // but this would be too expensive for a runtime checker

        if (!str_ends_with($type, '[]')) {
            return $type;
        }

        if ($returnType instanceof \ReflectionNamedType) {
            $type = $returnType->getName();

            if ('mixed' !== $type) {
                return isset(self::SPECIAL_RETURN_TYPES[$type]) ? $type : '\\'.$type;
            }
        }

        return 'array';
    }

    /**
     * Utility method to add #[ReturnTypeWillChange] where php triggers deprecations.
     */
    private function patchReturnTypeWillChange(\ReflectionMethod $method): void
    {
        if (\count($method->getAttributes(\ReturnTypeWillChange::class))) {
            return;
        }

        if (!is_file($file = $method->getFileName())) {
            return;
        }

        $fileOffset = self::$fileOffsets[$file] ?? 0;

        $code = file($file);

        $startLine = $method->getStartLine() + $fileOffset - 2;

        if (false !== stripos($code[$startLine], 'ReturnTypeWillChange')) {
            return;
        }

        $code[$startLine] .= "    #[\\ReturnTypeWillChange]\n";
        self::$fileOffsets[$file] = 1 + $fileOffset;
        file_put_contents($file, $code);
    }

    /**
     * Utility method to add @return annotations to the Symfony code-base where it triggers self-deprecations.
     */
    private function patchMethod(\ReflectionMethod $method, string $returnType, string $declaringFile, string $normalizedType): void
    {
        static $patchedMethods = [];
        static $useStatements = [];

        if (!is_file($file = $method->getFileName()) || isset($patchedMethods[$file][$startLine = $method->getStartLine()])) {
            return;
        }

        $patchedMethods[$file][$startLine] = true;
        $fileOffset = self::$fileOffsets[$file] ?? 0;
        $startLine += $fileOffset - 2;
        if ($nullable = str_ends_with($returnType, '|null')) {
            $returnType = substr($returnType, 0, -5);
        }
        $glue = str_contains($returnType, '&') ? '&' : '|';
        $returnType = explode($glue, $returnType);
        $code = file($file);

        foreach ($returnType as $i => $type) {
            if (preg_match('/((?:\[\])+)$/', $type, $m)) {
                $type = substr($type, 0, -\strlen($m[1]));
                $format = '%s'.$m[1];
            } else {
                $format = null;
            }

            if (isset(self::SPECIAL_RETURN_TYPES[$type]) || ('\\' === $type[0] && !$p = strrpos($type, '\\', 1))) {
                continue;
            }

            [$namespace, $useOffset, $useMap] = $useStatements[$file] ??= self::getUseStatements($file);

            if ('\\' !== $type[0]) {
                [$declaringNamespace, , $declaringUseMap] = $useStatements[$declaringFile] ??= self::getUseStatements($declaringFile);

                $p = strpos($type, '\\', 1);
                $alias = $p ? substr($type, 0, $p) : $type;

                if (isset($declaringUseMap[$alias])) {
                    $type = '\\'.$declaringUseMap[$alias].($p ? substr($type, $p) : '');
                } else {
                    $type = '\\'.$declaringNamespace.$type;
                }

                $p = strrpos($type, '\\', 1);
            }

            $alias = substr($type, 1 + $p);
            $type = substr($type, 1);

            if (!isset($useMap[$alias]) && (class_exists($c = $namespace.$alias) || interface_exists($c) || trait_exists($c))) {
                $useMap[$alias] = $c;
            }

            if (!isset($useMap[$alias])) {
                $useStatements[$file][2][$alias] = $type;
                $code[$useOffset] = "use $type;\n".$code[$useOffset];
                ++$fileOffset;
            } elseif ($useMap[$alias] !== $type) {
                $alias .= 'FIXME';
                $useStatements[$file][2][$alias] = $type;
                $code[$useOffset] = "use $type as $alias;\n".$code[$useOffset];
                ++$fileOffset;
            }

            $returnType[$i] = null !== $format ? \sprintf($format, $alias) : $alias;
        }

        if ('docblock' === $this->patchTypes['force'] || ('object' === $normalizedType && '7.1' === $this->patchTypes['php'])) {
            $returnType = implode($glue, $returnType).($nullable ? '|null' : '');

            if (str_contains($code[$startLine], '#[')) {
                --$startLine;
            }

            if ($method->getDocComment()) {
                $code[$startLine] = "     * @return $returnType\n".$code[$startLine];
            } else {
                $code[$startLine] .= <<<EOTXT
                        /**
                         * @return $returnType
                         */

                    EOTXT;
            }

            $fileOffset += substr_count($code[$startLine], "\n") - 1;
        }

        self::$fileOffsets[$file] = $fileOffset;
        file_put_contents($file, $code);

        $this->fixReturnStatements($method, $normalizedType);
    }

    private static function getUseStatements(string $file): array
    {
        $namespace = '';
        $useMap = [];
        $useOffset = 0;

        if (!is_file($file)) {
            return [$namespace, $useOffset, $useMap];
        }

        $file = file($file);

        for ($i = 0; $i < \count($file); ++$i) {
            if (preg_match('/^(class|interface|trait|abstract) /', $file[$i])) {
                break;
            }

            if (str_starts_with($file[$i], 'namespace ')) {
                $namespace = substr($file[$i], \strlen('namespace '), -2).'\\';
                $useOffset = $i + 2;
            }

            if (str_starts_with($file[$i], 'use ')) {
                $useOffset = $i;

                for (; str_starts_with($file[$i], 'use '); ++$i) {
                    $u = explode(' as ', substr($file[$i], 4, -2), 2);

                    if (1 === \count($u)) {
                        $p = strrpos($u[0], '\\');
                        $useMap[substr($u[0], false !== $p ? 1 + $p : 0)] = $u[0];
                    } else {
                        $useMap[$u[1]] = $u[0];
                    }
                }

                break;
            }
        }

        return [$namespace, $useOffset, $useMap];
    }

    private function fixReturnStatements(\ReflectionMethod $method, string $returnType): void
    {
        if ('docblock' !== $this->patchTypes['force']) {
            if ('7.1' === $this->patchTypes['php'] && 'object' === ltrim($returnType, '?')) {
                return;
            }

            if ('7.4' > $this->patchTypes['php'] && $method->hasReturnType()) {
                return;
            }

            if ('8.0' > $this->patchTypes['php'] && (str_contains($returnType, '|') || \in_array($returnType, ['mixed', 'static'], true))) {
                return;
            }

            if ('8.1' > $this->patchTypes['php'] && str_contains($returnType, '&')) {
                return;
            }
        }

        if (!is_file($file = $method->getFileName())) {
            return;
        }

        $fixedCode = $code = file($file);
        $i = (self::$fileOffsets[$file] ?? 0) + $method->getStartLine();

        if ('?' !== $returnType && 'docblock' !== $this->patchTypes['force']) {
            $fixedCode[$i - 1] = preg_replace('/\)(?::[^;\n]++)?(;?\n)/', "): $returnType\\1", $code[$i - 1]);
        }

        $end = $method->isGenerator() ? $i : $method->getEndLine();
        $inClosure = false;
        $braces = 0;
        for (; $i < $end; ++$i) {
            if (!$inClosure) {
                $inClosure = str_contains($code[$i], 'function (');
            }

            if ($inClosure) {
                $braces += substr_count($code[$i], '{') - substr_count($code[$i], '}');
                $inClosure = $braces > 0;

                continue;
            }

            if ('void' === $returnType) {
                $fixedCode[$i] = str_replace('    return null;', '    return;', $code[$i]);
            } elseif ('mixed' === $returnType || '?' === $returnType[0]) {
                $fixedCode[$i] = str_replace('    return;', '    return null;', $code[$i]);
            } else {
                $fixedCode[$i] = str_replace('    return;', "    return $returnType!?;", $code[$i]);
            }
        }

        if ($fixedCode !== $code) {
            file_put_contents($file, $fixedCode);
        }
    }

    /**
     * @param \ReflectionClass|\ReflectionMethod|\ReflectionProperty $reflector
     */
    private function parsePhpDoc(\Reflector $reflector): array
    {
        if (!$doc = $reflector->getDocComment()) {
            return [];
        }

        $tagName = '';
        $tagContent = '';

        $tags = [];

        foreach (explode("\n", substr($doc, 3, -2)) as $line) {
            $line = ltrim($line);
            $line = ltrim($line, '*');

            if ('' === $line = trim($line)) {
                if ('' !== $tagName) {
                    $tags[$tagName][] = $tagContent;
                }
                $tagName = $tagContent = '';
                continue;
            }

            if ('@' === $line[0]) {
                if ('' !== $tagName) {
                    $tags[$tagName][] = $tagContent;
                    $tagContent = '';
                }

                if (preg_match('{^@([-a-zA-Z0-9_:]++)(\s|$)}', $line, $m)) {
                    $tagName = $m[1];
                    $tagContent = str_replace("\t", ' ', ltrim(substr($line, 2 + \strlen($tagName))));
                } else {
                    $tagName = '';
                }
            } elseif ('' !== $tagName) {
                $tagContent .= ' '.str_replace("\t", ' ', $line);
            }
        }

        if ('' !== $tagName) {
            $tags[$tagName][] = $tagContent;
        }

        foreach ($tags['method'] ?? [] as $i => $method) {
            unset($tags['method'][$i]);

            $parts = preg_split('{(\s++|\((?:[^()]*+|(?R))*\)(?: *: *[^ ]++)?|<(?:[^<>]*+|(?R))*>|\{(?:[^{}]*+|(?R))*\})}', $method, -1, \PREG_SPLIT_DELIM_CAPTURE);
            $returnType = '';
            $static = 'static' === $parts[0];

            for ($i = $static ? 2 : 0; null !== $p = $parts[$i] ?? null; $i += 2) {
                if (\in_array($p, ['', 'callable'], true) || \in_array(substr($returnType, -1), ['|', '&'], true) || \in_array($p[0], ['|', '&'], true)) {
                    $returnType .= trim($parts[$i - 1] ?? '').$p;
                    continue;
                }

                $signature = '(' === ($parts[$i + 1][0] ?? '(') ? $parts[$i + 1] ?? '()' : null;

                if (null === $signature && '' === $returnType) {
                    $returnType = $p;
                    continue;
                }

                if ($static && 2 === $i) {
                    $static = false;
                    $returnType = 'static';
                }

                if (\in_array($description = trim(implode('', \array_slice($parts, 2 + $i))), ['', '.'], true)) {
                    $description = null;
                } elseif (!preg_match('/[.!]$/', $description)) {
                    $description .= '.';
                }

                $tags['method'][$p] = [$static, $returnType, $signature ?? '()', $description];
                break;
            }
        }

        foreach ($tags['param'] ?? [] as $i => $param) {
            unset($tags['param'][$i]);

            if (\strlen($param) !== strcspn($param, '<{(')) {
                $param = preg_replace('{\(([^()]*+|(?R))*\)(?: *: *[^ ]++)?|<([^<>]*+|(?R))*>|\{([^{}]*+|(?R))*\}}', '', $param);
            }

            if (false === $i = strpos($param, '$')) {
                continue;
            }

            $type = 0 === $i ? '' : rtrim(substr($param, 0, $i), ' &');
            $param = substr($param, 1 + $i, (strpos($param, ' ', $i) ?: (1 + $i + \strlen($param))) - $i - 1);

            $tags['param'][$param] = $type;
        }

        foreach (['var', 'return'] as $k) {
            if (null === $v = $tags[$k][0] ?? null) {
                continue;
            }
            if (\strlen($v) !== strcspn($v, '<{(')) {
                $v = preg_replace('{\(([^()]*+|(?R))*\)(?: *: *[^ ]++)?|<([^<>]*+|(?R))*>|\{([^{}]*+|(?R))*\}}', '', $v);
            }

            $tags[$k] = substr($v, 0, strpos($v, ' ') ?: \strlen($v)) ?: null;
        }

        return $tags;
    }
}
