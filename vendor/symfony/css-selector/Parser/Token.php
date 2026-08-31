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

/**
 * CSS selector token.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class Token
{
    public const TYPE_FILE_END = 'eof';
    public const TYPE_DELIMITER = 'delimiter';
    public const TYPE_WHITESPACE = 'whitespace';
    public const TYPE_IDENTIFIER = 'identifier';
    public const TYPE_HASH = 'hash';
    public const TYPE_NUMBER = 'number';
    public const TYPE_STRING = 'string';

    /**
     * @param self::TYPE_*|null $type
     */
    public function __construct(
        private ?string $type,
        private ?string $value,
        private ?int $position,
    ) {
    }

    /**
     * @return self::TYPE_*|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function isFileEnd(): bool
    {
        return self::TYPE_FILE_END === $this->type;
    }

    public function isDelimiter(array $values = []): bool
    {
        if (self::TYPE_DELIMITER !== $this->type) {
            return false;
        }

        if (!$values) {
            return true;
        }

        return \in_array($this->value, $values, true);
    }

    public function isWhitespace(): bool
    {
        return self::TYPE_WHITESPACE === $this->type;
    }

    public function isIdentifier(): bool
    {
        return self::TYPE_IDENTIFIER === $this->type;
    }

    public function isHash(): bool
    {
        return self::TYPE_HASH === $this->type;
    }

    public function isNumber(): bool
    {
        return self::TYPE_NUMBER === $this->type;
    }

    public function isString(): bool
    {
        return self::TYPE_STRING === $this->type;
    }

    public function __toString(): string
    {
        if ($this->value) {
            return \sprintf('<%s "%s" at %s>', $this->type, $this->value, $this->position);
        }

        return \sprintf('<%s at %s>', $this->type, $this->position);
    }
}
