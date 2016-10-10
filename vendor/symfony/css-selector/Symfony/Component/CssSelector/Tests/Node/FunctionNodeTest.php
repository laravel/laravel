<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\Node;

use Symfony\Component\CssSelector\Node\ElementNode;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Symfony\Component\CssSelector\Parser\Token;

class FunctionNodeTest extends AbstractNodeTest
{
    public function getToStringConversionTestData()
    {
        return array(
            array(new FunctionNode(new ElementNode(), 'function'), 'Function[Element[*]:function()]'),
            array(new FunctionNode(new ElementNode(), 'function', array(
                new Token(Token::TYPE_IDENTIFIER, 'value', 0),
            )), "Function[Element[*]:function(['value'])]"),
            array(new FunctionNode(new ElementNode(), 'function', array(
                new Token(Token::TYPE_STRING, 'value1', 0),
                new Token(Token::TYPE_NUMBER, 'value2', 0),
            )), "Function[Element[*]:function(['value1', 'value2'])]"),
        );
    }

    public function getSpecificityValueTestData()
    {
        return array(
            array(new FunctionNode(new ElementNode(), 'function'), 10),
            array(new FunctionNode(new ElementNode(), 'function', array(
                new Token(Token::TYPE_IDENTIFIER, 'value', 0),
            )), 10),
            array(new FunctionNode(new ElementNode(), 'function', array(
                new Token(Token::TYPE_STRING, 'value1', 0),
                new Token(Token::TYPE_NUMBER, 'value2', 0),
            )), 10),
        );
    }
}
