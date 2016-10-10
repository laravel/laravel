<?php

abstract class PHPParser_Tests_CodeTestAbstract extends PHPUnit_Framework_TestCase
{
    protected function getTests($directory, $fileExtension) {
        $it = new RecursiveDirectoryIterator($directory);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::LEAVES_ONLY);
        $it = new RegexIterator($it, '(\.' . preg_quote($fileExtension) . '$)');

        $tests = array();
        foreach ($it as $file) {
            // read file
            $fileContents = file_get_contents($file);

            // evaluate @@{expr}@@ expressions
            $fileContents = preg_replace_callback(
                '/@@\{(.*?)\}@@/',
                array($this, 'evalCallback'),
                $fileContents
            );

            // parse sections
            $parts = array_map('trim', explode('-----', $fileContents));

            // first part is the name
            $name = array_shift($parts);

            // multiple sections possible with always two forming a pair
            foreach (array_chunk($parts, 2) as $chunk) {
                $tests[] = array($name, $chunk[0], $chunk[1]);
            }
        }

        return $tests;
    }

    protected function evalCallback($matches) {
        return eval('return ' . $matches[1] . ';');
    }

    protected function canonicalize($str) {
        // trim from both sides
        $str = trim($str);

        // normalize EOL to \n
        $str = str_replace(array("\r\n", "\r"), "\n", $str);

        // trim right side of all lines
        return implode("\n", array_map('rtrim', explode("\n", $str)));
    }
}