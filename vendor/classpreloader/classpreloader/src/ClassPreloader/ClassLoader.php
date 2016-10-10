<?php

namespace ClassPreloader;

require_once __DIR__ . '/ClassNode.php';
require_once __DIR__ . '/ClassList.php';

/**
 * Creates an autoloader that intercepts and keeps track of each include in
 * order that files must be included. This autoloader proxies to all other
 * underlying autoloaders.
 */
class ClassLoader
{
    /**
     * @var ClassList List of loaded classes
     */
    public $classList;

    /**
     * Create the dependency list
     */
    public function __construct()
    {
        $this->classList = new ClassList();
    }

    /**
     * Wrap a block of code in the autoloader and get a list of loaded classes
     *
     * @param \Callable $func Callable function
     *
     * @return Config
     */
    public static function getIncludes($func)
    {
        $loader = new self();
        call_user_func($func, $loader);
        $loader->unregister();

        $config = new Config();
        foreach ($loader->getFilenames() as $file) {
            $config->addFile($file);
        }

        return $config;
    }

    /**
     * Registers this instance as an autoloader.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'), true, true);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return bool|null True, if loaded
     */
    public function loadClass($class)
    {
        foreach (spl_autoload_functions() as $func) {
            if (is_array($func) && $func[0] === $this) {
                continue;
            }
            $this->classList->push($class);
            if (call_user_func($func, $class)) {
                break;
            }
        }

        $this->classList->next();

        return true;
    }

    /**
     * Get an array of loaded file names in order of loading
     *
     * @return array
     */
    public function getFilenames()
    {
        $files = array();
        foreach ($this->classList->getClasses() as $class) {
            // Push interfaces before classes if not already loaded
            $r = new \ReflectionClass($class);
            foreach ($r->getInterfaces() as $inf) {
                $name = $inf->getFileName();
                if ($name && !in_array($name, $files)) {
                    $files[] = $name;
                }
            }
            $files[] = $r->getFileName();
        }

        return $files;
    }
}
