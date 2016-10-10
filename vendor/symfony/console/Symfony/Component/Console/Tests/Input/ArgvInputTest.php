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

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ArgvInputTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $_SERVER['argv'] = array('cli.php', 'foo');
        $input = new ArgvInput();
        $r = new \ReflectionObject($input);
        $p = $r->getProperty('tokens');
        $p->setAccessible(true);

        $this->assertEquals(array('foo'), $p->getValue($input), '__construct() automatically get its input from the argv server variable');
    }

    public function testParseArguments()
    {
        $input = new ArgvInput(array('cli.php', 'foo'));
        $input->bind(new InputDefinition(array(new InputArgument('name'))));
        $this->assertEquals(array('name' => 'foo'), $input->getArguments(), '->parse() parses required arguments');

        $input->bind(new InputDefinition(array(new InputArgument('name'))));
        $this->assertEquals(array('name' => 'foo'), $input->getArguments(), '->parse() is stateless');
    }

    /**
     * @dataProvider provideOptions
     */
    public function testParseOptions($input, $options, $expectedOptions, $message)
    {
        $input = new ArgvInput($input);
        $input->bind(new InputDefinition($options));

        $this->assertEquals($expectedOptions, $input->getOptions(), $message);
    }

    public function provideOptions()
    {
        return array(
            array(
                array('cli.php', '--foo'),
                array(new InputOption('foo')),
                array('foo' => true),
                '->parse() parses long options without a value',
            ),
            array(
                array('cli.php', '--foo=bar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED)),
                array('foo' => 'bar'),
                '->parse() parses long options with a required value (with a = separator)',
            ),
            array(
                array('cli.php', '--foo', 'bar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED)),
                array('foo' => 'bar'),
                '->parse() parses long options with a required value (with a space separator)',
            ),
            array(
                array('cli.php', '-f'),
                array(new InputOption('foo', 'f')),
                array('foo' => true),
                '->parse() parses short options without a value',
            ),
            array(
                array('cli.php', '-fbar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED)),
                array('foo' => 'bar'),
                '->parse() parses short options with a required value (with no separator)',
            ),
            array(
                array('cli.php', '-f', 'bar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED)),
                array('foo' => 'bar'),
                '->parse() parses short options with a required value (with a space separator)',
            ),
            array(
                array('cli.php', '-f', ''),
                array(new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL)),
                array('foo' => ''),
                '->parse() parses short options with an optional empty value',
            ),
            array(
                array('cli.php', '-f', '', 'foo'),
                array(new InputArgument('name'), new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL)),
                array('foo' => ''),
                '->parse() parses short options with an optional empty value followed by an argument',
            ),
            array(
                array('cli.php', '-f', '', '-b'),
                array(new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL), new InputOption('bar', 'b')),
                array('foo' => '', 'bar' => true),
                '->parse() parses short options with an optional empty value followed by an option',
            ),
            array(
                array('cli.php', '-f', '-b', 'foo'),
                array(new InputArgument('name'), new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL), new InputOption('bar', 'b')),
                array('foo' => null, 'bar' => true),
                '->parse() parses short options with an optional value which is not present',
            ),
            array(
                array('cli.php', '-fb'),
                array(new InputOption('foo', 'f'), new InputOption('bar', 'b')),
                array('foo' => true, 'bar' => true),
                '->parse() parses short options when they are aggregated as a single one',
            ),
            array(
                array('cli.php', '-fb', 'bar'),
                array(new InputOption('foo', 'f'), new InputOption('bar', 'b', InputOption::VALUE_REQUIRED)),
                array('foo' => true, 'bar' => 'bar'),
                '->parse() parses short options when they are aggregated as a single one and the last one has a required value',
            ),
            array(
                array('cli.php', '-fb', 'bar'),
                array(new InputOption('foo', 'f'), new InputOption('bar', 'b', InputOption::VALUE_OPTIONAL)),
                array('foo' => true, 'bar' => 'bar'),
                '->parse() parses short options when they are aggregated as a single one and the last one has an optional value',
            ),
            array(
                array('cli.php', '-fbbar'),
                array(new InputOption('foo', 'f'), new InputOption('bar', 'b', InputOption::VALUE_OPTIONAL)),
                array('foo' => true, 'bar' => 'bar'),
                '->parse() parses short options when they are aggregated as a single one and the last one has an optional value with no separator',
            ),
            array(
                array('cli.php', '-fbbar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL), new InputOption('bar', 'b', InputOption::VALUE_OPTIONAL)),
                array('foo' => 'bbar', 'bar' => null),
                '->parse() parses short options when they are aggregated as a single one and one of them takes a value',
            ),
        );
    }

    /**
     * @dataProvider provideInvalidInput
     */
    public function testInvalidInput($argv, $definition, $expectedExceptionMessage)
    {
        $this->setExpectedException('RuntimeException', $expectedExceptionMessage);

        $input = new ArgvInput($argv);
        $input->bind($definition);
    }

    public function provideInvalidInput()
    {
        return array(
            array(
                array('cli.php', '--foo'),
                new InputDefinition(array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED))),
                'The "--foo" option requires a value.',
            ),
            array(
                array('cli.php', '-f'),
                new InputDefinition(array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED))),
                'The "--foo" option requires a value.',
            ),
            array(
                array('cli.php', '-ffoo'),
                new InputDefinition(array(new InputOption('foo', 'f', InputOption::VALUE_NONE))),
                'The "-o" option does not exist.',
            ),
            array(
                array('cli.php', '--foo=bar'),
                new InputDefinition(array(new InputOption('foo', 'f', InputOption::VALUE_NONE))),
                'The "--foo" option does not accept a value.',
            ),
            array(
                array('cli.php', 'foo', 'bar'),
                new InputDefinition(),
                'Too many arguments.',
            ),
            array(
                array('cli.php', '--foo'),
                new InputDefinition(),
                'The "--foo" option does not exist.',
            ),
            array(
                array('cli.php', '-f'),
                new InputDefinition(),
                'The "-f" option does not exist.',
            ),
            array(
                array('cli.php', '-1'),
                new InputDefinition(array(new InputArgument('number'))),
                'The "-1" option does not exist.',
            ),
        );
    }

    public function testParseArrayArgument()
    {
        $input = new ArgvInput(array('cli.php', 'foo', 'bar', 'baz', 'bat'));
        $input->bind(new InputDefinition(array(new InputArgument('name', InputArgument::IS_ARRAY))));

        $this->assertEquals(array('name' => array('foo', 'bar', 'baz', 'bat')), $input->getArguments(), '->parse() parses array arguments');
    }

    public function testParseArrayOption()
    {
        $input = new ArgvInput(array('cli.php', '--name=foo', '--name=bar', '--name=baz'));
        $input->bind(new InputDefinition(array(new InputOption('name', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY))));

        $this->assertEquals(array('name' => array('foo', 'bar', 'baz')), $input->getOptions(), '->parse() parses array options ("--option=value" syntax)');

        $input = new ArgvInput(array('cli.php', '--name', 'foo', '--name', 'bar', '--name', 'baz'));
        $input->bind(new InputDefinition(array(new InputOption('name', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY))));
        $this->assertEquals(array('name' => array('foo', 'bar', 'baz')), $input->getOptions(), '->parse() parses array options ("--option value" syntax)');

        $input = new ArgvInput(array('cli.php', '--name=foo', '--name=bar', '--name='));
        $input->bind(new InputDefinition(array(new InputOption('name', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY))));
        $this->assertSame(array('name' => array('foo', 'bar', null)), $input->getOptions(), '->parse() parses empty array options as null ("--option=value" syntax)');

        $input = new ArgvInput(array('cli.php', '--name', 'foo', '--name', 'bar', '--name', '--anotherOption'));
        $input->bind(new InputDefinition(array(
            new InputOption('name', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY),
            new InputOption('anotherOption', null, InputOption::VALUE_NONE),
        )));
        $this->assertSame(array('name' => array('foo', 'bar', null), 'anotherOption' => true), $input->getOptions(), '->parse() parses empty array options as null ("--option value" syntax)');
    }

    public function testParseNegativeNumberAfterDoubleDash()
    {
        $input = new ArgvInput(array('cli.php', '--', '-1'));
        $input->bind(new InputDefinition(array(new InputArgument('number'))));
        $this->assertEquals(array('number' => '-1'), $input->getArguments(), '->parse() parses arguments with leading dashes as arguments after having encountered a double-dash sequence');

        $input = new ArgvInput(array('cli.php', '-f', 'bar', '--', '-1'));
        $input->bind(new InputDefinition(array(new InputArgument('number'), new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL))));
        $this->assertEquals(array('foo' => 'bar'), $input->getOptions(), '->parse() parses arguments with leading dashes as options before having encountered a double-dash sequence');
        $this->assertEquals(array('number' => '-1'), $input->getArguments(), '->parse() parses arguments with leading dashes as arguments after having encountered a double-dash sequence');
    }

    public function testParseEmptyStringArgument()
    {
        $input = new ArgvInput(array('cli.php', '-f', 'bar', ''));
        $input->bind(new InputDefinition(array(new InputArgument('empty'), new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL))));

        $this->assertEquals(array('empty' => ''), $input->getArguments(), '->parse() parses empty string arguments');
    }

    public function testGetFirstArgument()
    {
        $input = new ArgvInput(array('cli.php', '-fbbar'));
        $this->assertEquals('', $input->getFirstArgument(), '->getFirstArgument() returns the first argument from the raw input');

        $input = new ArgvInput(array('cli.php', '-fbbar', 'foo'));
        $this->assertEquals('foo', $input->getFirstArgument(), '->getFirstArgument() returns the first argument from the raw input');
    }

    public function testHasParameterOption()
    {
        $input = new ArgvInput(array('cli.php', '-f', 'foo'));
        $this->assertTrue($input->hasParameterOption('-f'), '->hasParameterOption() returns true if the given short option is in the raw input');

        $input = new ArgvInput(array('cli.php', '--foo', 'foo'));
        $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if the given short option is in the raw input');

        $input = new ArgvInput(array('cli.php', 'foo'));
        $this->assertFalse($input->hasParameterOption('--foo'), '->hasParameterOption() returns false if the given short option is not in the raw input');

        $input = new ArgvInput(array('cli.php', '--foo=bar'));
        $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if the given option with provided value is in the raw input');
    }

    public function testToString()
    {
        $input = new ArgvInput(array('cli.php', '-f', 'foo'));
        $this->assertEquals('-f foo', (string) $input);

        $input = new ArgvInput(array('cli.php', '-f', '--bar=foo', 'a b c d', "A\nB'C"));
        $this->assertEquals('-f --bar=foo '.escapeshellarg('a b c d').' '.escapeshellarg("A\nB'C"), (string) $input);
    }

    /**
     * @dataProvider provideGetParameterOptionValues
     */
    public function testGetParameterOptionEqualSign($argv, $key, $expected)
    {
        $input = new ArgvInput($argv);
        $this->assertEquals($expected, $input->getParameterOption($key), '->getParameterOption() returns the expected value');
    }

    public function provideGetParameterOptionValues()
    {
        return array(
            array(array('app/console', 'foo:bar', '-e', 'dev'), '-e', 'dev'),
            array(array('app/console', 'foo:bar', '--env=dev'), '--env', 'dev'),
            array(array('app/console', 'foo:bar', '-e', 'dev'), array('-e', '--env'), 'dev'),
            array(array('app/console', 'foo:bar', '--env=dev'), array('-e', '--env'), 'dev'),
            array(array('app/console', 'foo:bar', '--env=dev', '--en=1'), array('--en'), '1'),
        );
    }

    public function testParseSingleDashAsArgument()
    {
        $input = new ArgvInput(array('cli.php', '-'));
        $input->bind(new InputDefinition(array(new InputArgument('file'))));
        $this->assertEquals(array('file' => '-'), $input->getArguments(), '->parse() parses single dash as an argument');
    }
}
