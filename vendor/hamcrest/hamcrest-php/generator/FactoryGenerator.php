<?php

/*
 Copyright (c) 2009 hamcrest.org
 */

/**
 * Controls the process of extracting @factory doctags
 * and generating factory method files.
 *
 * Uses File_Iterator to scan for PHP files.
 */
class FactoryGenerator
{
    /**
     * Path to the Hamcrest PHP files to process.
     *
     * @var string
     */
    private $path;

    /**
     * @var array of FactoryFile
     */
    private $factoryFiles;

    public function __construct($path)
    {
        $this->path = $path;
        $this->factoryFiles = array();
    }

    public function addFactoryFile(FactoryFile $factoryFile)
    {
        $this->factoryFiles[] = $factoryFile;
    }

    public function generate()
    {
        $classes = $this->getClassesWithFactoryMethods();
        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                foreach ($method->getCalls() as $call) {
                    foreach ($this->factoryFiles as $file) {
                        $file->addCall($call);
                    }
                }
            }
        }
    }

    public function write()
    {
        foreach ($this->factoryFiles as $file) {
            $file->build();
            $file->write();
        }
    }

    public function getClassesWithFactoryMethods()
    {
        $classes = array();
        $files = $this->getSortedFiles();
        foreach ($files as $file) {
            $class = $this->getFactoryClass($file);
            if ($class !== null) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    public function getSortedFiles()
    {
        $iter = $this->getFileIterator();
        $files = array();
        foreach ($iter as $file) {
            $files[] = $file;
        }
        sort($files, SORT_STRING);

        return $files;
    }

    private function getFileIterator()
    {
        $factoryClass = class_exists('File_Iterator_Factory') ? 'File_Iterator_Factory' : 'SebastianBergmann\FileIterator\Factory';

        $factory = new $factoryClass();

        return $factory->getFileIterator($this->path, '.php');
    }

    public function getFactoryClass($file)
    {
        $name = $this->getFactoryClassName($file);
        if ($name !== null) {
            require_once $file;

            if (class_exists($name)) {
                $class = new FactoryClass(substr($file, strpos($file, 'Hamcrest/')), new ReflectionClass($name));
                if ($class->isFactory()) {
                    return $class;
                }
            }
        }

        return null;
    }

    public function getFactoryClassName($file)
    {
        $content = file_get_contents($file);
        if (preg_match('/namespace\s+(.+);/', $content, $namespace)
            && preg_match('/\n\s*class\s+(\w+)\s+extends\b/', $content, $className)
            && preg_match('/@factory\b/', $content)
        ) {
            return $namespace[1] . '\\' . $className[1];
        }

        return null;
    }
}
