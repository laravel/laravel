<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\Parser\Handler;

use Symfony\Component\CssSelector\Parser\Handler\StringHandler;
use Symfony\Component\CssSelector\Parser\Token;
use Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerPatterns;
use Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerEscaping;

class StringHandlerTest extends AbstractHandlerTest
{
    public function getHandleValueTestData()
    {
        return array(
            array('"hello"', new Token(Token::TYPE_STRING, 'hello', 1), ''),
            array('"1"', new Token(Token::TYPE_STRING, '1', 1), ''),
            array('" "', new Token(Token::TYPE_STRING, ' ', 1), ''),
            array('""', new Token(Token::TYPE_STRING, '', 1), ''),
            array("'hello'", new Token(Token::TYPE_STRING, 'hello', 1), ''),

            array("'foo'bar", new Token(Token::TYPE_STRING, 'foo', 1), 'bar'),
        );
    }

    public function getDontHandleValueTestData()
    {
        return array(
            array('hello'),
            array('>'),
            array('1'),
            array(' '),
        );
    }

    protected function generateHandler()
    {
        $patterns = new TokenizerPatterns();

        return new StringHandler($patterns, new TokenizerEscaping($patterns));
    }
}
