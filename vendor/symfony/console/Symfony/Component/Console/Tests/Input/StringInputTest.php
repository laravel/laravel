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

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;

class StringInputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTokenizeData
     */
    public function testTokenize($input, $tokens, $message)
    {
        $input = new StringInput($input);
        $r = new \ReflectionClass('Symfony\Component\Console\Input\ArgvInput');
        $p = $r->getProperty('tokens');
        $p->setAccessible(true);
        $this->assertEquals($tokens, $p->getValue($input), $message);
    }

    public function testInputOptionWithGivenString()
    {
        $definition = new InputDefinition(
            array(new InputOption('foo', null, InputOption::VALUE_REQUIRED))
        );

        // call to bind
        $input = new StringInput('--foo=bar');
        $input->bind($definition);
        $this->assertEquals('bar', $input->getOption('foo'));

        // definition in constructor
        $input = new StringInput('--foo=bar', $definition);
        $this->assertEquals('bar', $input->getOption('foo'));
    }

    public function getTokenizeData()
    {
        return array(
            array('', array(), '->tokenize() parses an empty string'),
            array('foo', array('foo'), '->tokenize() parses arguments'),
            array('  foo  bar  ', array('foo', 'bar'), '->tokenize() ignores whitespaces between arguments'),
            array('"quoted"', array('quoted'), '->tokenize() parses quoted arguments'),
            array("'quoted'", array('quoted'), '->tokenize() parses quoted arguments'),
            array("'a\rb\nc\td'", array("a\rb\nc\td"), '->tokenize() parses whitespace chars in strings'),
            array("'a'\r'b'\n'c'\t'd'", array('a','b','c','d'), '->tokenize() parses whitespace chars between args as spaces'),
            array('\"quoted\"', array('"quoted"'), '->tokenize() parses escaped-quoted arguments'),
            array("\'quoted\'", array('\'quoted\''), '->tokenize() parses escaped-quoted arguments'),
            array('-a', array('-a'), '->tokenize() parses short options'),
            array('-azc', array('-azc'), '->tokenize() parses aggregated short options'),
            array('-awithavalue', array('-awithavalue'), '->tokenize() parses short options with a value'),
            array('-a"foo bar"', array('-afoo bar'), '->tokenize() parses short options with a value'),
            array('-a"foo bar""foo bar"', array('-afoo barfoo bar'), '->tokenize() parses short options with a value'),
            array('-a\'foo bar\'', array('-afoo bar'), '->tokenize() parses short options with a value'),
            array('-a\'foo bar\'\'foo bar\'', array('-afoo barfoo bar'), '->tokenize() parses short options with a value'),
            array('-a\'foo bar\'"foo bar"', array('-afoo barfoo bar'), '->tokenize() parses short options with a value'),
            array('--long-option', array('--long-option'), '->tokenize() parses long options'),
            array('--long-option=foo', array('--long-option=foo'), '->tokenize() parses long options with a value'),
            array('--long-option="foo bar"', array('--long-option=foo bar'), '->tokenize() parses long options with a value'),
            array('--long-option="foo bar""another"', array('--long-option=foo baranother'), '->tokenize() parses long options with a value'),
            array('--long-option=\'foo bar\'', array('--long-option=foo bar'), '->tokenize() parses long options with a value'),
            array("--long-option='foo bar''another'", array("--long-option=foo baranother"), '->tokenize() parses long options with a value'),
            array("--long-option='foo bar'\"another\"", array("--long-option=foo baranother"), '->tokenize() parses long options with a value'),
            array('foo -a -ffoo --long bar', array('foo', '-a', '-ffoo', '--long', 'bar'), '->tokenize() parses when several arguments and options'),
        );
    }

    public function testToString()
    {
        $input = new StringInput('-f foo');
        $this->assertEquals('-f foo', (string) $input);

        $input = new StringInput('-f --bar=foo "a b c d"');
        $this->assertEquals('-f --bar=foo '.escapeshellarg('a b c d'), (string) $input);

        $input = new StringInput('-f --bar=foo \'a b c d\' '."'A\nB\\'C'");
        $this->assertEquals('-f --bar=foo '.escapeshellarg('a b c d').' '.escapeshellarg("A\nB'C"), (string) $input);
    }
}
