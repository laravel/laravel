<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\Parser;

use Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Symfony\Component\CssSelector\Node\SelectorNode;
use Symfony\Component\CssSelector\Parser\Parser;
use Symfony\Component\CssSelector\Parser\Token;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider getParserTestData */
    public function testParser($source, $representation)
    {
        $parser = new Parser();

        $this->assertEquals($representation, array_map(function (SelectorNode $node) {
            return (string) $node->getTree();
        }, $parser->parse($source)));
    }

    /** @dataProvider getParserExceptionTestData */
    public function testParserException($source, $message)
    {
        $parser = new Parser();

        try {
            $parser->parse($source);
            $this->fail('Parser should throw a SyntaxErrorException.');
        } catch (SyntaxErrorException $e) {
            $this->assertEquals($message, $e->getMessage());
        }
    }

    /** @dataProvider getPseudoElementsTestData */
    public function testPseudoElements($source, $element, $pseudo)
    {
        $parser = new Parser();
        $selectors = $parser->parse($source);
        $this->assertCount(1, $selectors);

        /** @var SelectorNode $selector */
        $selector = $selectors[0];
        $this->assertEquals($element, (string) $selector->getTree());
        $this->assertEquals($pseudo, (string) $selector->getPseudoElement());
    }

    /** @dataProvider getSpecificityTestData */
    public function testSpecificity($source, $value)
    {
        $parser = new Parser();
        $selectors = $parser->parse($source);
        $this->assertCount(1, $selectors);

        /** @var SelectorNode $selector */
        $selector = $selectors[0];
        $this->assertEquals($value, $selector->getSpecificity()->getValue());
    }

    /** @dataProvider getParseSeriesTestData */
    public function testParseSeries($series, $a, $b)
    {
        $parser = new Parser();
        $selectors = $parser->parse(sprintf(':nth-child(%s)', $series));
        $this->assertCount(1, $selectors);

        /** @var FunctionNode $function */
        $function = $selectors[0]->getTree();
        $this->assertEquals(array($a, $b), Parser::parseSeries($function->getArguments()));
    }

    /** @dataProvider getParseSeriesExceptionTestData */
    public function testParseSeriesException($series)
    {
        $parser = new Parser();
        $selectors = $parser->parse(sprintf(':nth-child(%s)', $series));
        $this->assertCount(1, $selectors);

        /** @var FunctionNode $function */
        $function = $selectors[0]->getTree();
        $this->setExpectedException('Symfony\Component\CssSelector\Exception\SyntaxErrorException');
        Parser::parseSeries($function->getArguments());
    }

    public function getParserTestData()
    {
        return array(
            array('*', array('Element[*]')),
            array('*|*', array('Element[*]')),
            array('*|foo', array('Element[foo]')),
            array('foo|*', array('Element[foo|*]')),
            array('foo|bar', array('Element[foo|bar]')),
            array('#foo#bar', array('Hash[Hash[Element[*]#foo]#bar]')),
            array('div>.foo', array('CombinedSelector[Element[div] > Class[Element[*].foo]]')),
            array('div> .foo', array('CombinedSelector[Element[div] > Class[Element[*].foo]]')),
            array('div >.foo', array('CombinedSelector[Element[div] > Class[Element[*].foo]]')),
            array('div > .foo', array('CombinedSelector[Element[div] > Class[Element[*].foo]]')),
            array("div \n>  \t \t .foo", array('CombinedSelector[Element[div] > Class[Element[*].foo]]')),
            array('td.foo,.bar', array('Class[Element[td].foo]', 'Class[Element[*].bar]')),
            array('td.foo, .bar', array('Class[Element[td].foo]', 'Class[Element[*].bar]')),
            array("td.foo\t\r\n\f ,\t\r\n\f .bar", array('Class[Element[td].foo]', 'Class[Element[*].bar]')),
            array('td.foo,.bar', array('Class[Element[td].foo]', 'Class[Element[*].bar]')),
            array('td.foo, .bar', array('Class[Element[td].foo]', 'Class[Element[*].bar]')),
            array("td.foo\t\r\n\f ,\t\r\n\f .bar", array('Class[Element[td].foo]', 'Class[Element[*].bar]')),
            array('div, td.foo, div.bar span', array('Element[div]', 'Class[Element[td].foo]', 'CombinedSelector[Class[Element[div].bar] <followed> Element[span]]')),
            array('div > p', array('CombinedSelector[Element[div] > Element[p]]')),
            array('td:first', array('Pseudo[Element[td]:first]')),
            array('td :first', array('CombinedSelector[Element[td] <followed> Pseudo[Element[*]:first]]')),
            array('a[name]', array('Attribute[Element[a][name]]')),
            array("a[ name\t]", array('Attribute[Element[a][name]]')),
            array('a [name]', array('CombinedSelector[Element[a] <followed> Attribute[Element[*][name]]]')),
            array('a[rel="include"]', array("Attribute[Element[a][rel = 'include']]")),
            array('a[rel = include]', array("Attribute[Element[a][rel = 'include']]")),
            array("a[hreflang |= 'en']", array("Attribute[Element[a][hreflang |= 'en']]")),
            array('a[hreflang|=en]', array("Attribute[Element[a][hreflang |= 'en']]")),
            array('div:nth-child(10)', array("Function[Element[div]:nth-child(['10'])]")),
            array(':nth-child(2n+2)', array("Function[Element[*]:nth-child(['2', 'n', '+2'])]")),
            array('div:nth-of-type(10)', array("Function[Element[div]:nth-of-type(['10'])]")),
            array('div div:nth-of-type(10) .aclass', array("CombinedSelector[CombinedSelector[Element[div] <followed> Function[Element[div]:nth-of-type(['10'])]] <followed> Class[Element[*].aclass]]")),
            array('label:only', array('Pseudo[Element[label]:only]')),
            array('a:lang(fr)', array("Function[Element[a]:lang(['fr'])]")),
            array('div:contains("foo")', array("Function[Element[div]:contains(['foo'])]")),
            array('div#foobar', array('Hash[Element[div]#foobar]')),
            array('div:not(div.foo)', array('Negation[Element[div]:not(Class[Element[div].foo])]')),
            array('td ~ th', array('CombinedSelector[Element[td] ~ Element[th]]')),
            array('.foo[data-bar][data-baz=0]', array("Attribute[Attribute[Class[Element[*].foo][data-bar]][data-baz = '0']]")),
        );
    }

    public function getParserExceptionTestData()
    {
        return array(
            array('attributes(href)/html/body/a', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, '(', 10))->getMessage()),
            array('attributes(href)', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, '(', 10))->getMessage()),
            array('html/body/a', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, '/', 4))->getMessage()),
            array(' ', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_FILE_END, '', 1))->getMessage()),
            array('div, ', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_FILE_END, '', 5))->getMessage()),
            array(' , div', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, ',', 1))->getMessage()),
            array('p, , div', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, ',', 3))->getMessage()),
            array('div > ', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_FILE_END, '', 6))->getMessage()),
            array('  > div', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, '>', 2))->getMessage()),
            array('foo|#bar', SyntaxErrorException::unexpectedToken('identifier or "*"', new Token(Token::TYPE_HASH, 'bar', 4))->getMessage()),
            array('#.foo', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, '#', 0))->getMessage()),
            array('.#foo', SyntaxErrorException::unexpectedToken('identifier', new Token(Token::TYPE_HASH, 'foo', 1))->getMessage()),
            array(':#foo', SyntaxErrorException::unexpectedToken('identifier', new Token(Token::TYPE_HASH, 'foo', 1))->getMessage()),
            array('[*]', SyntaxErrorException::unexpectedToken('"|"', new Token(Token::TYPE_DELIMITER, ']', 2))->getMessage()),
            array('[foo|]', SyntaxErrorException::unexpectedToken('identifier', new Token(Token::TYPE_DELIMITER, ']', 5))->getMessage()),
            array('[#]', SyntaxErrorException::unexpectedToken('identifier or "*"', new Token(Token::TYPE_DELIMITER, '#', 1))->getMessage()),
            array('[foo=#]', SyntaxErrorException::unexpectedToken('string or identifier', new Token(Token::TYPE_DELIMITER, '#', 5))->getMessage()),
            array(':nth-child()', SyntaxErrorException::unexpectedToken('at least one argument', new Token(Token::TYPE_DELIMITER, ')', 11))->getMessage()),
            array('[href]a', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_IDENTIFIER, 'a', 6))->getMessage()),
            array('[rel:stylesheet]', SyntaxErrorException::unexpectedToken('operator', new Token(Token::TYPE_DELIMITER, ':', 4))->getMessage()),
            array('[rel=stylesheet', SyntaxErrorException::unexpectedToken('"]"', new Token(Token::TYPE_FILE_END, '', 15))->getMessage()),
            array(':lang(fr', SyntaxErrorException::unexpectedToken('an argument', new Token(Token::TYPE_FILE_END, '', 8))->getMessage()),
            array(':contains("foo', SyntaxErrorException::unclosedString(10)->getMessage()),
            array('foo!', SyntaxErrorException::unexpectedToken('selector', new Token(Token::TYPE_DELIMITER, '!', 3))->getMessage()),
        );
    }

    public function getPseudoElementsTestData()
    {
        return array(
            array('foo', 'Element[foo]', ''),
            array('*', 'Element[*]', ''),
            array(':empty', 'Pseudo[Element[*]:empty]', ''),
            array(':BEfore', 'Element[*]', 'before'),
            array(':aftER', 'Element[*]', 'after'),
            array(':First-Line', 'Element[*]', 'first-line'),
            array(':First-Letter', 'Element[*]', 'first-letter'),
            array('::befoRE', 'Element[*]', 'before'),
            array('::AFter', 'Element[*]', 'after'),
            array('::firsT-linE', 'Element[*]', 'first-line'),
            array('::firsT-letteR', 'Element[*]', 'first-letter'),
            array('::Selection', 'Element[*]', 'selection'),
            array('foo:after', 'Element[foo]', 'after'),
            array('foo::selection', 'Element[foo]', 'selection'),
            array('lorem#ipsum ~ a#b.c[href]:empty::selection', 'CombinedSelector[Hash[Element[lorem]#ipsum] ~ Pseudo[Attribute[Class[Hash[Element[a]#b].c][href]]:empty]]', 'selection'),
        );
    }

    public function getSpecificityTestData()
    {
        return array(
            array('*', 0),
            array(' foo', 1),
            array(':empty ', 10),
            array(':before', 1),
            array('*:before', 1),
            array(':nth-child(2)', 10),
            array('.bar', 10),
            array('[baz]', 10),
            array('[baz="4"]', 10),
            array('[baz^="4"]', 10),
            array('#lipsum', 100),
            array(':not(*)', 0),
            array(':not(foo)', 1),
            array(':not(.foo)', 10),
            array(':not([foo])', 10),
            array(':not(:empty)', 10),
            array(':not(#foo)', 100),
            array('foo:empty', 11),
            array('foo:before', 2),
            array('foo::before', 2),
            array('foo:empty::before', 12),
            array('#lorem + foo#ipsum:first-child > bar:first-line', 213),
        );
    }

    public function getParseSeriesTestData()
    {
        return array(
            array('1n+3', 1, 3),
            array('1n +3', 1, 3),
            array('1n + 3', 1, 3),
            array('1n+ 3', 1, 3),
            array('1n-3', 1, -3),
            array('1n -3', 1, -3),
            array('1n - 3', 1, -3),
            array('1n- 3', 1, -3),
            array('n-5', 1, -5),
            array('odd', 2, 1),
            array('even', 2, 0),
            array('3n', 3, 0),
            array('n', 1, 0),
            array('+n', 1, 0),
            array('-n', -1, 0),
            array('5', 0, 5),
        );
    }

    public function getParseSeriesExceptionTestData()
    {
        return array(
            array('foo'),
            array('n+'),
        );
    }
}
