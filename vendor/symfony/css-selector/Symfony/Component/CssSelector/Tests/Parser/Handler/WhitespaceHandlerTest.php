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

use Symfony\Component\CssSelector\Parser\Handler\WhitespaceHandler;
use Symfony\Component\CssSelector\Parser\Token;

class WhitespaceHandlerTest extends AbstractHandlerTest
{
    public function getHandleValueTestData()
    {
        return array(
            array(' ', new Token(Token::TYPE_WHITESPACE, ' ', 0), ''),
            array("\n", new Token(Token::TYPE_WHITESPACE, "\n", 0), ''),
            array("\t", new Token(Token::TYPE_WHITESPACE, "\t", 0), ''),

            array(' foo', new Token(Token::TYPE_WHITESPACE, ' ', 0), 'foo'),
            array(' .foo', new Token(Token::TYPE_WHITESPACE, ' ', 0), '.foo'),
        );
    }

    public function getDontHandleValueTestData()
    {
        return array(
            array('>'),
            array('1'),
            array('a'),
        );
    }

    protected function generateHandler()
    {
        return new WhitespaceHandler();
    }
}
