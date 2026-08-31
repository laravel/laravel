<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Autoload;

/**
 * ClassLoader implements a PSR-0, PSR-4 and classmap class loader.
 *
 *     $loader = new \Composer\Autoload\ClassLoader();
 *
 *     // register classes with namespaces
 *     $loader->add('Symfony\Component', __DIR__.'/component');
 *     $loader->add('Symfony',           __DIR__.'/framework');
 *
 *     // activate the autoloader
 *     $loader->register();
 *
 *     // to enable searching the include path (eg. for PEAR packages)
 *     $loader->setUseIncludePath(true);
 *
 * In this example, if you try to use a class in the Symfony\Component
 * namespace or one of its children (Symfony\Component\Console for instance),
 * the autoloader will first look for the class under the component/
 * directory, and it will then fallback to the framework/ directory if not
 * found before giving up.
 *
 * This class is loosely based on the Symfony UniversalClassLoader.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @see    https://www.php-fig.org/psr/psr-0/
 * @see    https://www.php-fig.org/psr/psr-4/
 */
class ClassLoader
{
    /** @var \Closure(string):void */
    private static $includeFile;

    /** @var string|null */
    private $vendorDir;

    // PSR-4
    /**
     * @var array<string, array<string, int>>
     */
    private $prefixLengthsPsr4 = array();
    /**
     * @var array<string, list<string>>
     */
    private $prefixDirsPsr4 = array();
    /**
     * @var list<string>
     */
    private $fallbackDirsPsr4 = array();

    // PSR-0
    /**
     * List of PSR-0 prefixes
     *
     * Structured as array('F (first letter)' => array('Foo\Bar (full prefix)' => array('path', 'path2')))
     *
     * @var array<string, array<string, list<string>>>
     */
    private $prefixesPsr0 = array();
    /**
     * @var list<string>
     */
    private $fallbackDirsPsr0 = array();

    /** @var bool */
    private $useIncludePath = false;

    /**
     * @var array<string, string>
     */
    private $classMap = array();

    /** @var bool */
    private $classMapAuthoritative = false;

    /**
     * @var array<string, bool>
     */
    private $missingClasses = array();

    /** @var string|null */
    private $apcuPrefix;

    /**
     * @var array<string, self>
     */
    private static $registeredLoaders = array();

    /**
     * @param string|null $vendorDir
     */
    public function __construct($vendorDir = null)
    {
        $this->vendorDir = $vendorDir;
        self::initializeIncludeClosure();
    }

    /**
     * @return array<string, list<string>>
     */
    public function getPrefixes()
    {
        if (!empty($this->prefixesPsr0)) {
            return call_user_func_array('array_merge', array_values($this->prefixesPsr0));
        }

        return array();
    }

    /**
     * @return array<string, list<string>>
     */
    public function getPrefixesPsr4()
    {
        return $this->prefixDirsPsr4;
    }

    /**
     * @return list<string>
     */
    public function getFallbackDirs()
    {
        return $this->fallbackDirsPsr0;
    }

    /**
     * @return list<string>
     */
    public function getFallbackDirsPsr4()
    {
        return $this->fallbackDirsPsr4;
    }

    /**
     * @return array<string, string> Array of classname => path
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * @param array<string, string> $classMap Class to filename map
     *
     * @return void
     */
    public function addClassMap(array $classMap)
    {
        if ($this->classMap) {
            $this->classMap = array_merge($this->classMap, $classMap);
        } else {
            $this->classMap = $classMap;
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string              $prefix  The prefix
     * @param list<string>|string $paths   The PSR-0 root directories
     * @param bool                $prepend Whether to prepend the directories
     *
     * @return void
     */
    public function add($prefix, $paths, $prepend = false)
    {
        $paths = (array) $paths;
        if (!$prefix) {
            if ($prepend) {
                $this->fallbackDirsPsr0 = array_merge(
                    $paths,
                    $this->fallbackDirsPsr0
                );
            } else {
                $this->fallbackDirsPsr0 = array_merge(
                    $this->fallbackDirsPsr0,
                    $paths
                );
            }

            return;
        }

        $first = $prefix[0];
        if (!isset($this->prefixesPsr0[$first][$prefix])) {
            $this->prefixesPsr0[$first][$prefix] = $paths;

            return;
        }
        if ($prepend) {
            $this->prefixesPsr0[$first][$prefix] = array_merge(
                $paths,
                $this->prefixesPsr0[$first][$prefix]
            );
        } else {
            $this->prefixesPsr0[$first][$prefix] = array_merge(
                $this->prefixesPsr0[$first][$prefix],
                $paths
            );
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace, either
     * appending or prepending to the ones previously set for this namespace.
     *
     * @param string              $prefix  The prefix/namespace, with trailing '\\'
     * @param list<string>|string $paths   The PSR-4 base directories
     * @param bool                $prepend Whether to prepend the directories
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        $paths = (array) $paths;
        if (!$prefix) {
            // Register directories for the root namespace.
            if ($prepend) {
                $this->fallbackDirsPsr4 = array_merge(
                    $paths,
                    $this->fallbackDirsPsr4
                );
            } else {
                $this->fallbackDirsPsr4 = array_merge(
                    $this->fallbackDirsPsr4,
                    $paths
                );
            }
        } elseif (!isset($this->prefixDirsPsr4[$prefix])) {
            // Register directories for a new namespace.
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this->prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this->prefixDirsPsr4[$prefix] = $paths;
        } elseif ($prepend) {
            // Prepend directories for an already registered namespace.
            $this->prefixDirsPsr4[$prefix] = array_merge(
                $paths,
                $this->prefixDirsPsr4[$prefix]
            );
        } else {
            // Append directories for an already registered namespace.
            $this->prefixDirsPsr4[$prefix] = array_merge(
                $this->prefixDirsPsr4[$prefix],
                $paths
            );
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix,
     * replacing any others previously set for this prefix.
     *
     * @param string              $prefix The prefix
     * @param list<string>|string $paths  The PSR-0 base directories
     *
     * @return void
     */
    public function set($prefix, $paths)
    {
        if (!$prefix) {
            $this->fallbackDirsPsr0 = (array) $paths;
        } else {
            $this->prefixesPsr0[$prefix[0]][$prefix] = (array) $paths;
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace,
     * replacing any others previously set for this namespace.
     *
     * @param string              $prefix The prefix/namespace, with trailing '\\'
     * @param list<string>|string $paths  The PSR-4 base directories
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function setPsr4($prefix, $paths)
    {
        if (!$prefix) {
            $this->fallbackDirsPsr4 = (array) $paths;
        } else {
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this->prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this->prefixDirsPsr4[$prefix] = (array) $paths;
        }
    }

    /**
     * Turns on searching the include path for class files.
     *
     * @param bool $useIncludePath
     *
     * @return void
     */
    public function setUseIncludePath($useIncludePath)
    {
        $this->useIncludePath = $useIncludePath;
    }

    /**
     * Can be used to check if the autoloader uses the include path to check
     * for classes.
     *
     * @return bool
     */
    public function getUseIncludePath()
    {
        return $this->useIncludePath;
    }

    /**
     * Turns off searching the prefix and fallback directories for classes
     * that have not been registered with the class map.
     *
     * @param bool $classMapAuthoritative
     *
     * @return void
     */
    public function setClassMapAuthoritative($classMapAuthoritative)
    {
        $this->classMapAuthoritative = $classMapAuthoritative;
    }

    /**
     * Should class lookup fail if not found in the current class map?
     *
     * @return bool
     */
    public function isClassMapAuthoritative()
    {
        return $this->classMapAuthoritative;
    }

    /**
     * APCu prefix to use to cache found/not-found classes, if the extension is enabled.
     *
     * @param string|null $apcuPrefix
     *
     * @return void
     */
    public function setApcuPrefix($apcuPrefix)
    {
        $this->apcuPrefix = function_exists('apcu_fetch') && filter_var(ini_get('apc.enabled'), FILTER_VALIDATE_BOOLEAN) ? $apcuPrefix : null;
    }

    /**
     * The APCu prefix in use, or null if APCu caching is not enabled.
     *
     * @return string|null
     */
    public function getApcuPrefix()
    {
        return $this->apcuPrefix;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     *
     * @return void
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);

        if (null === $this->vendorDir) {
            return;
        }

        if ($prepend) {
            self::$registeredLoaders = array($this->vendorDir => $this) + self::$registeredLoaders;
        } else {
            unset(self::$registeredLoaders[$this->vendorDir]);
            self::$registeredLoaders[$this->vendorDir] = $this;
        }
    }

    /**
     * Unregisters this instance as an autoloader.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));

        if (null !== $this->vendorDir) {
            unset(self::$registeredLoaders[$this->vendorDir]);
        }
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return true|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            $includeFile = self::$includeFile;
            $includeFile($file);

            return true;
        }

        return null;
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        // class map lookup
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }
        if ($this->classMapAuthoritative || isset($this->missingClasses[$class])) {
            return false;
        }
        if (null !== $this->apcuPrefix) {
            $file = apcu_fetch($this->apcuPrefix.$class, $hit);
            if ($hit) {
                return $file;
            }
        }

        $file = $this->findFileWithExtension($class, '.php');

        // Search for Hack files if we are running on HHVM
        if (false === $file && defined('HHVM_VERSION')) {
            $file = $this->findFileWithExtension($class, '.hh');
        }

        if (null !== $this->apcuPrefix) {
            apcu_add($this->apcuPrefix.$class, $file);
        }

        if (false === $file) {
            // Remember that this class does not exist.
            $this->missingClasses[$class] = true;
        }

        return $file;
    }

    /**
     * Returns the currently registered loaders keyed by their corresponding vendor directories.
     *
     * @return array<string, self>
     */
    public static function getRegisteredLoaders()
    {
        return self::$registeredLoaders;
    }

    /**
     * @param  string       $class
     * @param  string       $ext
     * @return string|false
     */
    private function findFileWithExtension($class, $ext)
    {
        // PSR-4 lookup
        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . $ext;

        $first = $class[0];
        if (isset($this->prefixLengthsPsr4[$first])) {
            $subPath = $class;
            while (false !== $lastPos = strrpos($subPath, '\\')) {
                $subPath = substr($subPath, 0, $lastPos);
                $search = $subPath . '\\';
                if (isset($this->prefixDirsPsr4[$search])) {
                    $pathEnd = DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $lastPos + 1);
                    foreach ($this->prefixDirsPsr4[$search] as $dir) {
                        if (file_exists($file = $dir . $pathEnd)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        foreach ($this->fallbackDirsPsr4 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4)) {
                return $file;
            }
        }

        // PSR-0 lookup
        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
                . strtr(substr($logicalPathPsr4, $pos + 1), '_', DIRECTORY_SEPARATOR);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($class, '_', DIRECTORY_SEPARATOR) . $ext;
        }

        if (isset($this->prefixesPsr0[$first])) {
            foreach ($this->prefixesPsr0[$first] as $prefix => $dirs) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-0 fallback dirs
        foreach ($this->fallbackDirsPsr0 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                return $file;
            }
        }

        // PSR-0 include paths.
        if ($this->useIncludePath && $file = stream_resolve_include_path($logicalPathPsr0)) {
            return $file;
        }

        return false;
    }

    /**
     * @return void
     */
    private static function initializeIncludeClosure()
    {
        if (self::$includeFile !== null) {
            return;
        }

        /**
         * Scope isolated include.
         *
         * Prevents access to $this/self from included files.
         *
         * @param  string $file
         * @return void
         */
        self::$includeFile = \Closure::bind(static function($file) {
            include $file;
        }, null, null);
    }
}
