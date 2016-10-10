#!/usr/bin/env php
<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// -------------------------------------------------------------------------- //
// This script can be used to automatically glue all the .php files of Predis
// into a single monolithic script file that can be used without an autoloader,
// just like the other previous versions of the library.
//
// Much of its complexity is due to the fact that we cannot simply join PHP
// files, but namespaces and classes definitions must follow a precise order
// when dealing with subclassing and inheritance.
//
// The current implementation is pretty naïve, but it should do for now.
// -------------------------------------------------------------------------- //

class CommandLine
{
    public static function getOptions()
    {
        $parameters = array(
            's:'  => 'source:',
            'o:'  => 'output:',
            'e:'  => 'exclude:',
            'E:'  => 'exclude-classes:',
        );

        $getops = getopt(implode(array_keys($parameters)), $parameters);

        $options = array(
            'source'  => __DIR__ . "/../lib/",
            'output'  => PredisFile::NS_ROOT . '.php',
            'exclude' => array(),
        );

        foreach ($getops as $option => $value) {
            switch ($option) {
                case 's':
                case 'source':
                    $options['source'] = $value;
                    break;

                case 'o':
                case 'output':
                    $options['output'] = $value;
                    break;

                case 'E':
                case 'exclude-classes':
                    $options['exclude'] = @file($value, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: $value;
                    break;

                case 'e':
                case 'exclude':
                    $options['exclude'] = is_array($value) ? $value : array($value);
                    break;
            }
        }

        return $options;
    }
}

class PredisFile
{
    const NS_ROOT = 'Predis';

    private $namespaces;

    public function __construct()
    {
        $this->namespaces = array();
    }

    public static function from($libraryPath, Array $exclude = array())
    {
        $nsroot = self::NS_ROOT;
        $predisFile = new PredisFile();
        $libIterator = new RecursiveDirectoryIterator("$libraryPath$nsroot");

        foreach (new RecursiveIteratorIterator($libIterator) as $classFile)
        {
            if (!$classFile->isFile()) {
                continue;
            }

            $namespace = strtr(str_replace($libraryPath, '', $classFile->getPath()), '/', '\\');

            if (in_array(sprintf('%s\\%s', $namespace, $classFile->getBasename('.php')), $exclude)) {
                continue;
            }

            $phpNamespace = $predisFile->getNamespace($namespace);

            if ($phpNamespace === false) {
                $phpNamespace = new PhpNamespace($namespace);
                $predisFile->addNamespace($phpNamespace);
            }

            $phpClass = new PhpClass($phpNamespace, $classFile);
        }

        return $predisFile;
    }

    public function addNamespace(PhpNamespace $namespace)
    {
        if (isset($this->namespaces[(string)$namespace])) {
            throw new InvalidArgumentException("Duplicated namespace");
        }
        $this->namespaces[(string)$namespace] = $namespace;
    }

    public function getNamespaces()
    {
        return $this->namespaces;
    }

    public function getNamespace($namespace)
    {
        if (!isset($this->namespaces[$namespace])) {
            return false;
        }

        return $this->namespaces[$namespace];
    }

    public function getClassByFQN($classFqn)
    {
        if (($nsLastPos = strrpos($classFqn, '\\')) !== false) {
            $namespace = $this->getNamespace(substr($classFqn, 0, $nsLastPos));
            if ($namespace === false) {
                return null;
            }
            $className = substr($classFqn, $nsLastPos + 1);

            return $namespace->getClass($className);
        }

        return null;
    }

    private function calculateDependencyScores(&$classes, $fqn)
    {
        if (!isset($classes[$fqn])) {
            $classes[$fqn] = 0;
        }

        $classes[$fqn] += 1;

        if (($phpClass = $this->getClassByFQN($fqn)) === null) {
            throw new RuntimeException(
                "Cannot found the class $fqn which is required by other subclasses. Are you missing a file?"
            );
        }

        foreach ($phpClass->getDependencies() as $fqn) {
            $this->calculateDependencyScores($classes, $fqn);
        }
    }

    private function getDependencyScores()
    {
        $classes = array();

        foreach ($this->getNamespaces() as $phpNamespace) {
            foreach ($phpNamespace->getClasses() as $phpClass) {
                $this->calculateDependencyScores($classes, $phpClass->getFQN());
            }
        }

        return $classes;
    }

    private function getOrderedNamespaces($dependencyScores)
    {
        $namespaces = array_fill_keys(array_unique(
            array_map(
                function ($fqn) { return PhpNamespace::extractName($fqn); },
                array_keys($dependencyScores)
            )
        ), 0);

        foreach ($dependencyScores as $classFqn => $score) {
            $namespaces[PhpNamespace::extractName($classFqn)] += $score;
        }

        arsort($namespaces);

        return array_keys($namespaces);
    }

    private function getOrderedClasses(PhpNamespace $phpNamespace, $classes)
    {
        $nsClassesFQNs = array_map(function ($cl) { return $cl->getFQN(); }, $phpNamespace->getClasses());
        $nsOrderedClasses = array();

        foreach ($nsClassesFQNs as $nsClassFQN) {
            $nsOrderedClasses[$nsClassFQN] = $classes[$nsClassFQN];
        }

        arsort($nsOrderedClasses);

        return array_keys($nsOrderedClasses);
    }

    public function getPhpCode()
    {
        $buffer = array("<?php\n\n", PhpClass::LICENSE_HEADER, "\n\n");
        $classes = $this->getDependencyScores();
        $namespaces = $this->getOrderedNamespaces($classes);

        foreach ($namespaces as $namespace) {
            $phpNamespace = $this->getNamespace($namespace);

            // generate namespace directive
            $buffer[] = $phpNamespace->getPhpCode();
            $buffer[] = "\n";

            // generate use directives
            $useDirectives = $phpNamespace->getUseDirectives();
            if (count($useDirectives) > 0) {
                $buffer[] = $useDirectives->getPhpCode();
                $buffer[] = "\n";
            }

            // generate classes bodies
            $nsClasses = $this->getOrderedClasses($phpNamespace, $classes);
            foreach ($nsClasses as $classFQN) {
                $buffer[] = $this->getClassByFQN($classFQN)->getPhpCode();
                $buffer[] = "\n\n";
            }

            $buffer[] = "/* " . str_repeat("-", 75) . " */";
            $buffer[] = "\n\n";
        }

        return implode($buffer);
    }

    public function saveTo($outputFile)
    {
        // TODO: add more sanity checks
        if ($outputFile === null || $outputFile === '') {
            throw new InvalidArgumentException('You must specify a valid output file');
        }
        file_put_contents($outputFile, $this->getPhpCode());
    }
}

class PhpNamespace implements IteratorAggregate
{
    private $namespace;
    private $classes;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
        $this->classes = array();
        $this->useDirectives = new PhpUseDirectives($this);
    }

    public static function extractName($fqn)
    {
        $nsSepLast = strrpos($fqn, '\\');
        if ($nsSepLast === false) {
            return $fqn;
        }
        $ns = substr($fqn, 0, $nsSepLast);

        return $ns !== '' ? $ns : null;
    }

    public function addClass(PhpClass $class)
    {
        $this->classes[$class->getName()] = $class;
    }

    public function getClass($className)
    {
        if (isset($this->classes[$className])) {
            return $this->classes[$className];
        }
    }

    public function getClasses()
    {
        return array_values($this->classes);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getClasses());
    }

    public function getUseDirectives()
    {
        return $this->useDirectives;
    }

    public function getPhpCode()
    {
        return "namespace $this->namespace;\n";
    }

    public function __toString()
    {
        return $this->namespace;
    }
}

class PhpUseDirectives implements Countable, IteratorAggregate
{
    private $use;
    private $aliases;
    private $reverseAliases;
    private $namespace;

    public function __construct(PhpNamespace $namespace)
    {
        $this->namespace = $namespace;
        $this->use = array();
        $this->aliases = array();
        $this->reverseAliases = array();
    }

    public function add($use, $as = null)
    {
        if (in_array($use, $this->use)) {
            return;
        }

        $rename = null;
        $this->use[] = $use;
        $aliasedClassName = $as ?: PhpClass::extractName($use);

        if (isset($this->aliases[$aliasedClassName])) {
            $parentNs = $this->getParentNamespace();

            if ($parentNs && false !== $pos = strrpos($parentNs, '\\')) {
                $parentNs = substr($parentNs, $pos);
            }

            $newAlias = "{$parentNs}_{$aliasedClassName}";
            $rename = (object) array(
                'namespace' => $this->namespace,
                'from' => $aliasedClassName,
                'to' => $newAlias,
            );

            $this->aliases[$newAlias] = $use;
            $as = $newAlias;
        } else {
            $this->aliases[$aliasedClassName] = $use;
        }

        if ($as !== null) {
            $this->reverseAliases[$use] = $as;
        }

        return $rename;
    }

    public function getList()
    {
        return $this->use;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getList());
    }

    public function getPhpCode()
    {
        $reverseAliases = $this->reverseAliases;

        $reducer = function ($str, $use) use ($reverseAliases) {
            if (isset($reverseAliases[$use])) {
                return $str .= "use $use as {$reverseAliases[$use]};\n";
            } else {
                return $str .= "use $use;\n";
            }
        };

        return array_reduce($this->getList(), $reducer, '');
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getParentNamespace()
    {
        if (false !== $pos = strrpos($this->namespace, '\\')) {
            return substr($this->namespace, 0, $pos);
        }

        return '';
    }

    public function getFQN($className)
    {
        if (($nsSepFirst = strpos($className, '\\')) === false) {
            if (isset($this->aliases[$className])) {
                return $this->aliases[$className];
            }

            return (string)$this->getNamespace() . "\\$className";
        }

        if ($nsSepFirst != 0) {
            throw new InvalidArgumentException("Partially qualified names are not supported");
        }

        return $className;
    }

    public function count()
    {
        return count($this->use);
    }
}

class PhpClass
{
    const LICENSE_HEADER = <<<LICENSE
/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
LICENSE;

    private $namespace;
    private $file;
    private $body;
    private $implements;
    private $extends;
    private $name;

    public function __construct(PhpNamespace $namespace, SplFileInfo $classFile)
    {
        $this->namespace = $namespace;
        $this->file = $classFile;
        $this->implements = array();
        $this->extends = array();

        $this->extractData();
        $namespace->addClass($this);
    }

    public static function extractName($fqn)
    {
        $nsSepLast = strrpos($fqn, '\\');
        if ($nsSepLast === false) {
            return $fqn;
        }

        return substr($fqn, $nsSepLast + 1);
    }

    private function extractData()
    {
        $renames = array();
        $useDirectives = $this->getNamespace()->getUseDirectives();

        $useExtractor = function ($m) use ($useDirectives, &$renames) {
            array_shift($m);

            if (isset($m[1])) {
                $m[1] = str_replace(" as ", '', $m[1]);
            }

            if ($rename = call_user_func_array(array($useDirectives, 'add'), $m)) {
                $renames[] = $rename;
            }
        };

        $classBuffer = stream_get_contents(fopen($this->getFile()->getPathname(), 'r'));

        $classBuffer = str_replace(self::LICENSE_HEADER, '', $classBuffer);

        $classBuffer = preg_replace('/<\?php\s?\\n\s?/', '', $classBuffer);
        $classBuffer = preg_replace('/\s?\?>\n?/ms', '', $classBuffer);
        $classBuffer = preg_replace('/namespace\s+[\w\d_\\\\]+;\s?/', '', $classBuffer);
        $classBuffer = preg_replace_callback('/use\s+([\w\d_\\\\]+)(\s+as\s+.*)?;\s?\n?/', $useExtractor, $classBuffer);

        foreach ($renames as $rename) {
            $classBuffer = str_replace($rename->from, $rename->to, $classBuffer);
        }

        $this->body = trim($classBuffer);

        $this->extractHierarchy();
    }

    private function extractHierarchy()
    {
        $implements = array();
        $extends =  array();

        $extractor = function ($iterator, $callback) {
            $className = '';
            $iterator->seek($iterator->key() + 1);

            while ($iterator->valid()) {
                $token = $iterator->current();

                if (is_string($token)) {
                    if (preg_match('/\s?,\s?/', $token)) {
                        $callback(trim($className));
                        $className = '';
                    } else if ($token == '{') {
                        $callback(trim($className));
                        return;
                    }
                }

                switch ($token[0]) {
                    case T_NS_SEPARATOR:
                        $className .= '\\';
                        break;

                    case T_STRING:
                        $className .= $token[1];
                        break;

                    case T_IMPLEMENTS:
                    case T_EXTENDS:
                        $callback(trim($className));
                        $iterator->seek($iterator->key() - 1);
                        return;
                }

                $iterator->next();
            }
        };

        $tokens = token_get_all("<?php\n" . trim($this->getPhpCode()));
        $iterator = new ArrayIterator($tokens);

        while ($iterator->valid()) {
            $token = $iterator->current();
            if (is_string($token)) {
                $iterator->next();
                continue;
            }

            switch ($token[0]) {
                case T_CLASS:
                case T_INTERFACE:
                    $iterator->seek($iterator->key() + 2);
                    $tk = $iterator->current();
                    $this->name = $tk[1];
                    break;

                case T_IMPLEMENTS:
                    $extractor($iterator, function ($fqn) use (&$implements) {
                        $implements[] = $fqn;
                    });
                    break;

                case T_EXTENDS:
                    $extractor($iterator, function ($fqn) use (&$extends) {
                        $extends[] = $fqn;
                    });
                    break;
            }

            $iterator->next();
        }

        $this->implements = $this->guessFQN($implements);
        $this->extends = $this->guessFQN($extends);
    }

    public function guessFQN($classes)
    {
        $useDirectives = $this->getNamespace()->getUseDirectives();
        return array_map(array($useDirectives, 'getFQN'), $classes);
    }

    public function getImplementedInterfaces($all = false)
    {
        if ($all) {
            return $this->implements;
        }

        return array_filter(
            $this->implements,
            function ($cn) { return strpos($cn, 'Predis\\') === 0; }
        );
    }

    public function getExtendedClasses($all = false)
    {
        if ($all) {
            return $this->extemds;
        }

        return array_filter(
            $this->extends,
            function ($cn) { return strpos($cn, 'Predis\\') === 0; }
        );
    }

    public function getDependencies($all = false)
    {
        return array_merge(
            $this->getImplementedInterfaces($all),
            $this->getExtendedClasses($all)
        );
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFQN()
    {
        return (string)$this->getNamespace() . '\\' . $this->name;
    }

    public function getPhpCode()
    {
        return $this->body;
    }

    public function __toString()
    {
        return "class " . $this->getName() . '{ ... }';
    }
}

/* -------------------------------------------------------------------------- */

$options = CommandLine::getOptions();
$predisFile = PredisFile::from($options['source'], $options['exclude']);
$predisFile->saveTo($options['output']);
