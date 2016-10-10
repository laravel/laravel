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

use Symfony\Component\CssSelector\Parser\Token;
use Symfony\Component\CssSelector\Parser\TokenStream;

class TokenStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNext()
    {
        $stream = new TokenStream();
        $stream->push($t1 = new Token(Token::TYPE_IDENTIFIER, 'h1', 0));
        $stream->push($t2 = new Token(Token::TYPE_DELIMITER, '.', 2));
        $stream->push($t3 = new Token(Token::TYPE_IDENTIFIER, 'title', 3));

        $this->assertSame($t1, $stream->getNext());
        $this->assertSame($t2, $stream->getNext());
        $this->assertSame($t3, $stream->getNext());
    }

    public function testGetPeek()
    {
        $stream = new TokenStream();
        $stream->push($t1 = new Token(Token::TYPE_IDENTIFIER, 'h1', 0));
        $stream->push($t2 = new Token(Token::TYPE_DELIMITER, '.', 2));
        $stream->push($t3 = new Token(Token::TYPE_IDENTIFIER, 'title', 3));

        $this->assertSame($t1, $stream->getPeek());
        $this->assertSame($t1, $stream->getNext());
        $this->assertSame($t2, $stream->getPeek());
        $this->assertSame($t2, $stream->getPeek());
        $this->assertSame($t2, $stream->getNext());
    }

    public function testGetNextIdentifier()
    {
        $stream = new TokenStream();
        $stream->push(new Token(Token::TYPE_IDENTIFIER, 'h1', 0));

        $this->assertEquals('h1', $stream->getNextIdentifier());
    }

    public function testFailToGetNextIdentifier()
    {
        $this->setExpectedException('Symfony\Component\CssSelector\Exception\SyntaxErrorException');

        $stream = new TokenStream();
        $stream->push(new Token(Token::TYPE_DELIMITER, '.', 2));
        $stream->getNextIdentifier();
    }

    public function testGetNextIdentifierOrStar()
    {
        $stream = new TokenStream();

        $stream->push(new Token(Token::TYPE_IDENTIFIER, 'h1', 0));
        $this->assertEquals('h1', $stream->getNextIdentifierOrStar());

        $stream->push(new Token(Token::TYPE_DELIMITER, '*', 0));
        $this->assertNull($stream->getNextIdentifierOrStar());
    }

    public function testFailToGetNextIdentifierOrStar()
    {
        $this->setExpectedException('Symfony\Component\CssSelector\Exception\SyntaxErrorException');

        $stream = new TokenStream();
        $stream->push(new Token(Token::TYPE_DELIMITER, '.', 2));
        $stream->getNextIdentifierOrStar();
    }

    public function testSkipWhitespace()
    {
        $stream = new TokenStream();
        $stream->push($t1 = new Token(Token::TYPE_IDENTIFIER, 'h1', 0));
        $stream->push($t2 = new Token(Token::TYPE_WHITESPACE, ' ', 2));
        $stream->push($t3 = new Token(Token::TYPE_IDENTIFIER, 'h1', 3));

        $stream->skipWhitespace();
        $this->assertSame($t1, $stream->getNext());

        $stream->skipWhitespace();
        $this->assertSame($t3, $stream->getNext());
    }
}
