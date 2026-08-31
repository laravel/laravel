<?php declare(strict_types = 1);
namespace TheSeer\Tokenizer;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, Token>
 */
class TokenCollection implements IteratorAggregate, ArrayAccess, Countable {

    /** @var Token[] */
    private $tokens = [];

    public function addToken(Token $token): void {
        $this->tokens[] = $token;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->tokens);
    }

    public function count(): int {
        return \count($this->tokens);
    }

    public function offsetExists($offset): bool {
        return isset($this->tokens[$offset]);
    }

    /**
     * @throws TokenCollectionException
     */
    public function offsetGet($offset): Token {
        if (!$this->offsetExists($offset)) {
            throw new TokenCollectionException(
                \sprintf('No Token at offest %s', $offset)
            );
        }

        return $this->tokens[$offset];
    }

    /**
     * @param Token $value
     *
     * @throws TokenCollectionException
     */
    public function offsetSet($offset, $value): void {
        if (!\is_int($offset)) {
            $type = \gettype($offset);

            throw new TokenCollectionException(
                \sprintf(
                    'Offset must be of type integer, %s given',
                    $type === 'object' ? \get_class($value) : $type
                )
            );
        }

        if (!$value instanceof Token) {
            $type = \gettype($value);

            throw new TokenCollectionException(
                \sprintf(
                    'Value must be of type %s, %s given',
                    Token::class,
                    $type === 'object' ? \get_class($value) : $type
                )
            );
        }
        $this->tokens[$offset] = $value;
    }

    public function offsetUnset($offset): void {
        unset($this->tokens[$offset]);
    }
}
