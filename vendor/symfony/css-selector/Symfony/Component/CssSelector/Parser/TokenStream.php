<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Parser;

use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\CssSelector\Exception\SyntaxErrorException;

/**
 * CSS selector token stream.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class TokenStream
{
    /**
     * @var Token[]
     */
    private $tokens = array();

    /**
     * @var bool
     */
    private $frozen = false;

    /**
     * @var Token[]
     */
    private $used = array();

    /**
     * @var int
     */
    private $cursor = 0;

    /**
     * @var Token|null
     */
    private $peeked = null;

    /**
     * @var bool
     */
    private $peeking = false;

    /**
     * Pushes a token.
     *
     * @param Token $token
     *
     * @return TokenStream
     */
    public function push(Token $token)
    {
        $this->tokens[] = $token;

        return $this;
    }

    /**
     * Freezes stream.
     *
     * @return TokenStream
     */
    public function freeze()
    {
        $this->frozen = true;

        return $this;
    }

    /**
     * Returns next token.
     *
     * @throws InternalErrorException If there is no more token
     *
     * @return Token
     */
    public function getNext()
    {
        if ($this->peeking) {
            $this->peeking = false;
            $this->used[] = $this->peeked;

            return $this->peeked;
        }

        if (!isset($this->tokens[$this->cursor])) {
            throw new InternalErrorException('Unexpected token stream end.');
        }

        return $this->tokens[$this->cursor ++];
    }

    /**
     * Returns peeked token.
     *
     * @return Token
     */
    public function getPeek()
    {
        if (!$this->peeking) {
            $this->peeked = $this->getNext();
            $this->peeking = true;
        }

        return $this->peeked;
    }

    /**
     * Returns used tokens.
     *
     * @return Token[]
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Returns nex identifier token.
     *
     * @throws SyntaxErrorException If next token is not an identifier
     *
     * @return string The identifier token value
     */
    public function getNextIdentifier()
    {
        $next = $this->getNext();

        if (!$next->isIdentifier()) {
            throw SyntaxErrorException::unexpectedToken('identifier', $next);
        }

        return $next->getValue();
    }

    /**
     * Returns nex identifier or star delimiter token.
     *
     * @throws SyntaxErrorException If next token is not an identifier or a star delimiter
     *
     * @return null|string The identifier token value or null if star found
     */
    public function getNextIdentifierOrStar()
    {
        $next = $this->getNext();

        if ($next->isIdentifier()) {
            return $next->getValue();
        }

        if ($next->isDelimiter(array('*'))) {
            return;
        }

        throw SyntaxErrorException::unexpectedToken('identifier or "*"', $next);
    }

    /**
     * Skips next whitespace if any.
     */
    public function skipWhitespace()
    {
        $peek = $this->getPeek();

        if ($peek->isWhitespace()) {
            $this->getNext();
        }
    }
}
