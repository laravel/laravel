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
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class TokenStream
{
    /**
     * @var Token[]
     */
    private array $tokens = [];

    /**
     * @var Token[]
     */
    private array $used = [];

    private int $cursor = 0;
    private ?Token $peeked;
    private bool $peeking = false;

    /**
     * Pushes a token.
     *
     * @return $this
     */
    public function push(Token $token): static
    {
        $this->tokens[] = $token;

        return $this;
    }

    /**
     * Freezes stream.
     *
     * @return $this
     */
    public function freeze(): static
    {
        return $this;
    }

    /**
     * Returns next token.
     *
     * @throws InternalErrorException If there is no more token
     */
    public function getNext(): Token
    {
        if ($this->peeking) {
            $this->peeking = false;
            $this->used[] = $this->peeked;

            return $this->peeked;
        }

        if (!isset($this->tokens[$this->cursor])) {
            throw new InternalErrorException('Unexpected token stream end.');
        }

        return $this->tokens[$this->cursor++];
    }

    /**
     * Returns peeked token.
     */
    public function getPeek(): Token
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
    public function getUsed(): array
    {
        return $this->used;
    }

    /**
     * Returns next identifier token.
     *
     * @throws SyntaxErrorException If next token is not an identifier
     */
    public function getNextIdentifier(): string
    {
        $next = $this->getNext();

        if (!$next->isIdentifier()) {
            throw SyntaxErrorException::unexpectedToken('identifier', $next);
        }

        return $next->getValue();
    }

    /**
     * Returns next identifier or null if star delimiter token is found.
     *
     * @throws SyntaxErrorException If next token is not an identifier or a star delimiter
     */
    public function getNextIdentifierOrStar(): ?string
    {
        $next = $this->getNext();

        if ($next->isIdentifier()) {
            return $next->getValue();
        }

        if ($next->isDelimiter(['*'])) {
            return null;
        }

        throw SyntaxErrorException::unexpectedToken('identifier or "*"', $next);
    }

    /**
     * Skips next whitespace if any.
     */
    public function skipWhitespace(): void
    {
        $peek = $this->getPeek();

        if ($peek->isWhitespace()) {
            $this->getNext();
        }
    }
}
