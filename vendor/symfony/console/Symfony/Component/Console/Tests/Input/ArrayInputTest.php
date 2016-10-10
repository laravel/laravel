<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Input;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ArrayInputTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFirstArgument()
    {
        $input = new ArrayInput(array());
        $this->assertNull($input->getFirstArgument(), '->getFirstArgument() returns null if no argument were passed');
        $input = new ArrayInput(array('name' => 'Fabien'));
        $this->assertEquals('Fabien', $input->getFirstArgument(), '->getFirstArgument() returns the first passed argument');
        $input = new ArrayInput(array('--foo' => 'bar', 'name' => 'Fabien'));
        $this->assertEquals('Fabien', $input->getFirstArgument(), '->getFirstArgument() returns the first passed argument');
    }

    public function testHasParameterOption()
    {
        $input = new ArrayInput(array('name' => 'Fabien', '--foo' => 'bar'));
        $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if an option is present in the passed parameters');
        $this->assertFalse($input->hasParameterOption('--bar'), '->hasParameterOption() returns false if an option is not present in the passed parameters');

        $input = new ArrayInput(array('--foo'));
        $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if an option is present in the passed parameters');
    }

    public function testParseArguments()
    {
        $input = new ArrayInput(array('name' => 'foo'), new InputDefinition(array(new InputArgument('name'))));

        $this->assertEquals(array('name' => 'foo'), $input->getArguments(), '->parse() parses required arguments');
    }

    /**
     * @dataProvider provideOptions
     */
    public function testParseOptions($input, $options, $expectedOptions, $message)
    {
        $input = new ArrayInput($input, new InputDefinition($options));

        $this->assertEquals($expectedOptions, $input->getOptions(), $message);
    }

    public function provideOptions()
    {
        return array(
            array(
                array('--foo' => 'bar'),
                array(new InputOption('foo')),
                array('foo' => 'bar'),
                '->parse() parses long options',
            ),
            array(
                array('--foo' => 'bar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL, '', 'default')),
                array('foo' => 'bar'),
                '->parse() parses long options with a default value',
            ),
            array(
                array('--foo' => null),
                array(new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL, '', 'default')),
                array('foo' => 'default'),
                '->parse() parses long options with a default value',
            ),
            array(
                array('-f' => 'bar'),
                array(new InputOption('foo', 'f')),
                array('foo' => 'bar'),
                '->parse() parses short options',
            ),
        );
    }

    /**
     * @dataProvider provideInvalidInput
     */
    public function testParseInvalidInput($parameters, $definition, $expectedExceptionMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedExceptionMessage);

        new ArrayInput($parameters, $definition);
    }

    public function provideInvalidInput()
    {
        return array(
            array(
                array('foo' => 'foo'),
                new InputDefinition(array(new InputArgument('name'))),
                'The "foo" argument does not exist.',
            ),
            array(
                array('--foo' => null),
                new InputDefinition(array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED))),
                'The "--foo" option requires a value.',
            ),
            array(
                array('--foo' => 'foo'),
                new InputDefinition(),
                'The "--foo" option does not exist.',
            ),
            array(
                array('-o' => 'foo'),
                new InputDefinition(),
                'The "-o" option does not exist.',
            ),
        );
    }

    public function testToString()
    {
        $input = new ArrayInput(array('-f' => null, '-b' => 'bar', '--foo' => 'b a z', '--lala' => null, 'test' => 'Foo', 'test2' => "A\nB'C"));
        $this->assertEquals('-f -b=bar --foo='.escapeshellarg('b a z').' --lala Foo '.escapeshellarg("A\nB'C"), (string) $input);
    }
}
