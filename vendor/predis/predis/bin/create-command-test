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
// This script can be used to automatically generate a file with the scheleton
// of a test case to test a Redis command by specifying the name of the class
// in the Predis\Command namespace (only classes in this namespace are valid).
// For example, to generate a test case for SET (which is represented by the
// Predis\Command\StringSet class):
//
//   $ ./bin/generate-command-test.php --class=StringSet
//
// Here is a list of optional arguments:
//
// --realm: each command has its own realm (commands that operate on strings,
// lists, sets and such) but while this realm is usually inferred from the name
// of the specified class, sometimes it can be useful to override it with a
// custom one.
//
// --output: write the generated test case to the specified path instead of
// the default one.
//
// --overwrite: pre-existing test files are not overwritten unless this option
// is explicitly specified.
// -------------------------------------------------------------------------- //

use Predis\Command\CommandInterface;
use Predis\Command\PrefixableCommandInterface;

class CommandTestCaseGenerator
{
    private $options;

    public function __construct(Array $options)
    {
        if (!isset($options['class'])) {
            throw new RuntimeException("Missing 'class' option.");
        }
        $this->options = $options;
    }

    public static function fromCommandLine()
    {
        $parameters = array(
            'c:'  => 'class:',
            'r::' => 'realm::',
            'o::' => 'output::',
            'x::' => 'overwrite::'
        );

        $getops = getopt(implode(array_keys($parameters)), $parameters);

        $options = array(
            'overwrite' => false,
            'tests' => __DIR__.'/../tests',
        );

        foreach ($getops as $option => $value) {
            switch ($option) {
                case 'c':
                case 'class':
                    $options['class'] = $value;
                    break;

                case 'r':
                case 'realm':
                    $options['realm'] = $value;
                    break;

                case 'o':
                case 'output':
                    $options['output'] = $value;
                    break;

                case 'x':
                case 'overwrite':
                    $options['overwrite'] = true;
                    break;
            }
        }

        if (!isset($options['class'])) {
            throw new RuntimeException("Missing 'class' option.");
        }

        $options['fqn'] = "Predis\\Command\\{$options['class']}";
        $options['path'] = "Predis/Command/{$options['class']}.php";

        $source = __DIR__.'/../lib/'.$options['path'];
        if (!file_exists($source)) {
            throw new RuntimeException("Cannot find class file for {$options['fqn']} in $source.");
        }

        if (!isset($options['output'])) {
            $options['output'] = sprintf("%s/%s", $options['tests'], str_replace('.php', 'Test.php', $options['path']));
        }

        return new self($options);
    }

    protected function getTestRealm()
    {
        if (isset($this->options['realm'])) {
            if (!$this->options['realm']) {
                throw new RuntimeException('Invalid value for realm has been sepcified (empty).');
            }
            return $this->options['realm'];
        }

        $fqnParts = explode('\\', $this->options['fqn']);
        $class = array_pop($fqnParts);
        list($realm,) = preg_split('/([[:upper:]][[:lower:]]+)/', $class, 2, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        return strtolower($realm);
    }

    public function generate()
    {
        $reflection = new ReflectionClass($class = $this->options['fqn']);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Class $class must be instantiable, abstract classes or interfaces are not allowed.");
        }
        if (!$reflection->implementsInterface('Predis\Command\CommandInterface')) {
            throw new RuntimeException("Class $class must implement Predis\Command\CommandInterface.");
        }

        $instance = $reflection->newInstance();
        $buffer = $this->getTestCaseBuffer($instance);

        return $buffer;
    }

    public function save()
    {
        $options = $this->options;
        if (file_exists($options['output']) && !$options['overwrite']) {
            throw new RuntimeException("File {$options['output']} already exist. Specify the --overwrite option to overwrite the existing file.");
        }
        file_put_contents($options['output'], $this->generate());
    }

    protected function getTestCaseBuffer(CommandInterface $instance)
    {
        $id = $instance->getId();
        $fqn = get_class($instance);
        $fqnParts = explode('\\', $fqn);
        $class = array_pop($fqnParts) . "Test";
        $realm = $this->getTestRealm();

        $buffer =<<<PHP
<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

/**
 * @group commands
 * @group realm-$realm
 */
class $class extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return '$fqn';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return '$id';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        \$this->markTestIncomplete('This test has not been implemented yet.');

        \$arguments = array(/* add arguments */);
        \$expected = array(/* add arguments */);

        \$command = \$this->getCommand();
        \$command->setArguments(\$arguments);

        \$this->assertSame(\$expected, \$command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        \$this->markTestIncomplete('This test has not been implemented yet.');

        \$raw = null;
        \$expected = null;

        \$command = \$this->getCommand();

        \$this->assertSame(\$expected, \$command->parseResponse(\$raw));
    }

PHP;

        if ($instance instanceof PrefixableCommandInterface) {
            $buffer .=<<<PHP

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        \$this->markTestIncomplete('This test has not been implemented yet.');

        \$arguments = array(/* add arguments */);
        \$expected = array(/* add arguments */);

        \$command = \$this->getCommandWithArgumentsArray(\$arguments);
        \$command->prefixKeys('prefix:');

        \$this->assertSame(\$expected, \$command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeysIgnoredOnEmptyArguments()
    {
        \$command = \$this->getCommand();
        \$command->prefixKeys('prefix:');

        \$this->assertSame(array(), \$command->getArguments());
    }

PHP;
        }

        return "$buffer}\n";
    }
}

// ------------------------------------------------------------------------- //

require __DIR__.'/../autoload.php';

$generator = CommandTestCaseGenerator::fromCommandLine();
$generator->save();
