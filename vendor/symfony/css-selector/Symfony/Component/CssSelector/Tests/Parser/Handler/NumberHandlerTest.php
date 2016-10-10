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

use Symfony\Component\CssSelector\Parser\Handler\NumberHandler;
use Symfony\Component\CssSelector\Parser\Token;
use Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerPatterns;

class NumberHandlerTest extends AbstractHandlerTest
{
    public function getHandleValueTestData()
    {
        return array(
            array('12', new Token(Token::TYPE_NUMBER, '12', 0), ''),
            array('12.34', new Token(Token::TYPE_NUMBER, '12.34', 0), ''),
            array('+12.34', new Token(Token::TYPE_NUMBER, '+12.34', 0), ''),
            array('-12.34', new Token(Token::TYPE_NUMBER, '-12.34', 0), ''),

            array('12 arg', new Token(Token::TYPE_NUMBER, '12', 0), ' arg'),
            array('12]', new Token(Token::TYPE_NUMBER, '12', 0), ']'),
        );
    }

    public function getDontHandleValueTestData()
    {
        return array(
            array('hello'),
            array('>'),
            array('+'),
            array(' '),
            array('/* comment */'),
        );
    }

    protected function generateHandler()
    {
        $patterns = new TokenizerPatterns();

        return new NumberHandler($patterns);
    }
}
